<?php

namespace App\Service;

use Imagick;
use Exception;
use App\Entity\Upload;
use App\Utils\Password;
use Psr\Log\LoggerInterface;
use App\Repository\UploadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Uploader {

    protected EntityManagerInterface $em;
    protected LoggerInterface $logger;
    protected UploadRepository $uploads;
    protected ParameterBagInterface $params;

    public function __construct(UploadRepository $uploads, EntityManagerInterface $em, LoggerInterface $logger, ParameterBagInterface $params) {
        $this->em = $em;
        $this->logger = $logger;
        $this->uploads = $uploads;
        $this->params = $params;
    }

    public function upload(Request $request, array $info = []): ?Upload {
        if($file = $request->files->get('upload')) {
            return $this->save($file, [
                'name' => $file->getClientOriginalName(),
                'resize' => (int)$request->get('resize') ?: null,
                'lock' => (bool)$request->get('lock'),
                'ip' => $info['ip'] ?? 'unknown'
            ]);
        } elseif($upload = $request->get('upload')) {
            $upload['data'] = explode(',', $upload['data'], 2)[1];
            if(!$upload['data']) {
                throw new Exception('Пустой файл');
            }
            $tmp = '/tmp/' . uniqid('upload_') . '_' . $upload['name'];
            file_put_contents($tmp, base64_decode($upload['data']));
            $file = new File($tmp);
            return $this->save($file, [
                'name' => $upload['name'],
                'resize' => (int)$request->get('resize') ?: null,
                'lock' => (bool)$request->get('lock'),
                'ip' => $info['ip'] ?? 'unknown'
            ]);
        } else {
            return null;
        }
    }

    public function save(File $file, array $options = []): Upload {

        $id = $options['id'] ?? null;
        $name = $options['name'] ?? null;

        $id = $id ?: Password::fromChars(32, Password::CHARS_LETTERS_LC);
        $prefix = date('Ym');
        $name = $name ?: $file->getFilename();
        $ext = pathinfo($name, PATHINFO_EXTENSION);

        if(in_array(strtolower($ext), ['php', 'sh', 'pl', 'exe'])) {
            unlink($file->getPath());
            throw new AccessDeniedHttpException('Forbidden');
        }

        $fileName = $id . '.' . $ext;
        $movedFile = $file->move(__DIR__ . '/../../public/uploads/' . $prefix, $fileName);

        $size = $movedFile->getSize() ?: 0;
        $mime = $movedFile->getMimeType() ?: 'application/octet-stream';

        if(preg_match('/^image/', $mime)) {
            $im = new Imagick($movedFile->getPathname());
            if($options['resize'] && ($im->getImageWidth() > $options['resize'] || $im->getImageHeight() > $options['resize'])) {
                $im->resizeImage($options['resize'], $options['resize'], Imagick::FILTER_LANCZOS, 0.5, true);
                $im->writeImage();
                $size = filesize($movedFile->getPathname());
            }
        }

        $upload = new Upload($id, '/uploads/' . $prefix . '/' . $fileName, $name, $mime, $size, (bool)$options['lock']);
        $this->em->persist($upload);
        $this->em->flush();
        return $upload;
    }

}

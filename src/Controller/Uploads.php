<?php

namespace App\Controller;

use Imagick;
use App\Entity\Upload;
use App\Service\Rediska;
use App\Service\Uploader;
use App\Models\Uploads\UploadRequest;
use App\Models\Uploads\PartialUploadStatus;
use Yabx\TypeScriptBundle\Attributes\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use App\Models\Uploads\InitPartialUploadRequest;
use Yabx\TypeScriptBundle\Attributes\Controller;
use App\Models\Uploads\PartialChunkUploadRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Controller('Загрузки')]
class Uploads extends Base {

    #[Route('/uploads/{upload}')]
    public function getUploadInfo(Upload $upload): JsonResponse {
        return $this->result($upload, Response::HTTP_OK ,['upload:full']);
    }

    #[Method('Загрузить в Base64', request: UploadRequest::class, response: Upload::class)]
    #[Route('/upload/base64', methods: ['POST'])]
    public function uploadJson(Request $request, Uploader $uploader): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $result = $uploader->upload($request, ['ip' => $this->getClientIp()]);
        return $this->result($result, 200, $request->get('extra') ? ['upload:full'] : []);
    }

    #[Method('Загрузить с FormData', request: "FormData", response: Upload::class)]
    #[Route('/upload/form', methods: ['POST'])]
    public function uploadForm(Request $request, Uploader $uploader): JsonResponse {
        //$this->denyAccessUnlessGranted('ROLE_USER');
        $result = $uploader->upload($request, ['ip' => $this->getClientIp()]);
        return $this->result($result, 200, $request->get('extra') ? ['upload:full'] : []);
    }

    #[Method('Частичная загрузка (init)', request: InitPartialUploadRequest::class, response: PartialUploadStatus::class)]
    #[Route('/upload/partial', methods: ['POST'])]
    public function beginPartial(Rediska $redis, InitPartialUploadRequest $request): JsonResponse {
        $status = new PartialUploadStatus($request);
        $redis->set("upload:{$status->getId()}", $status);
        return $this->result($status);
    }

    #[Method('Частичная загрузка (chunk)', request: PartialChunkUploadRequest::class, response: PartialUploadStatus::class)]
    #[Route('/upload/partial', methods: ['PATCH'])]
    public function chunkPartial(Rediska $redis, PartialChunkUploadRequest $request, Uploader $uploader): JsonResponse {
        $id = $request->id;

        /** @var PartialUploadStatus|null $status */
        $status = $redis->get("upload:{$id}");
        if(!$status) {
            return $this->result('Upload not found', 404);
        }

        $status->processChunk($request);
        $redis->set("upload:{$id}", $status);

        if($status->isReady()) {
            $file = new File("/tmp/{$id}");
            $status->setUpload($uploader->save($file, [
                'name' => $status->getFileName(),
                'resize' => $status->getRequest()->resize,
                'lock' => $status->getRequest()->lock,
                'extra' => $status->getRequest()->extra,
            ]));
            $redis->del("upload:{$id}");
        }

        return $this->result($status, 200, $status->getRequest()->extra ? ['upload:full'] : []);
    }

    #[Method('Информация о файле', response: Upload::class)]
    #[Route('/upload/{file}', methods: ['GET'])]
    public function getUpload(Upload $file): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->result($file, 200, ['upload:full']);
    }

    #[Route('/download/{file}/{name}', methods: ['GET'])]
    #[Route('/download/{file}', methods: ['GET'])]
    public function download(?Upload $file, ?string $name): Response {
        if(!$file) {
            return $this->httpError(404);
        }
        $path = $file->getFullPath();
        if(!file_exists($path)) {
            return $this->httpError(404);
        }
        $res = new BinaryFileResponse($path);
        $res->headers->set('Content-Type', 'application/octet-stream');
        $res->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name ?: $file->getName());
        return $res;
    }

    #[Route('/thumb/{size}/{file}.{ext}')]
    public function thumb(Upload $file, int $size, string $ext): Response {
        if(str_starts_with($file->getMime(), 'image/')) {
            $im = new Imagick($file->getFullPath());
            if($im->getImageWidth() > $size) {
                $im->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1, true);
            }
            $path = __DIR__ . '/../../public/thumb/' . $size;
            if(!is_dir($path)) mkdir($path, 0755, true);
            $im->writeImage($path . '/' . $file->getId() . '.' . $ext);
            header('Content-Type: ' . $file->getMime());
            echo $im;
            exit;
        } else {
            return $this->httpError(404);
        }
    }

}

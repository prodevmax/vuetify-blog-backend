<?php

namespace App\UI\Rest\Controller;

use App\Domain\Article\File;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class FileController extends FOSRestController
{
    /**
     * @Rest\Post("/files/upload")
     * @param Request $request
     * @return View
     */
    public function upload(Request $request): View
    {

        /** @var UploadedFile $file */
        $file = $request->files->get('file') ?? '';

        $fileName = $this->generateUniqueFileName().'.'. $file->guessExtension();

        try {
            $file->move(
                $this->getParameter('files_upload_path'),
                $fileName
            );
        } catch (FileException $e) {
            return View::create(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        return View::create(['file_name' => $fileName], Response::HTTP_OK);
    }

    private function generateUniqueFileName():string
    {
        return md5(uniqid());
    }
}

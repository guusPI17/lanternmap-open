<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class SavingFile
{
    private $filesystem;
    private $fileFormat;
    private $filePath;
    private $fullFileName;

    public function __construct(
        string $fileFormat,
        Filesystem $filesystem
    ) {
        $this->fileFormat = $fileFormat;
        $this->filesystem = $filesystem;
    }

    public function getFullFileName(): string
    {
        return $this->fullFileName;
    }

    public function getFileFormat(): string
    {
        return $this->fileFormat;
    }

    public function setFileFormat(string $fileFormat): void
    {
        $this->fileFormat = $fileFormat;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    private function generationFullFileName(string $fileName): void
    {
        $this->fullFileName = $fileName .
            '-' . (new \DateTime())->format('d-m-Y H:i:s')
            . '.' . $this->fileFormat;
    }

    public function save(string $fileName, string $content): void
    {
        // составление названия файла
        $this->generationFullFileName($fileName);

        // сохранить сгенерированные данные передвижения
        $this->filesystem->dumpFile(
            $this->filePath . '/' . $this->fullFileName,
            $content
        );
    }
}

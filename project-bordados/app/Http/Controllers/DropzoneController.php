<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Illuminate\Support\Facades\File; // Asegúrate de importar esto
class DropzoneController extends Controller
{
    public function __invoke(Request $request)
    {


        // create the file receiver
        $receiver = new FileReceiver($request->archivo, $request, HandlerFactory::classFromRequest($request));

        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            return $this->saveFile($save->getFile());
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
        ]);
    }


    protected function saveFile(UploadedFile $file)
    {
        $fileName = $file->getClientOriginalName();
        // Group files by mime type
        $mime = $file->getMimeType();
        // Group files by the date (week
        $dateFolder = date("Y-m-W");

        // 🔥 Sanitizar el nombre del archivo ANTES de moverlo
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileName = strtolower(str_replace(' ', '_', $originalName)) . '.' . $extension;

        // Build the file path
        // $filePath = "upload/{$mime}/{$dateFolder}/";
        $filePath = "temp_uploads/{$mime}/{$dateFolder}/";
        $finalPath = storage_path("app/" . $filePath);

        // move the file name
        // $finalpath es para donde se va a mover el archivo
        $file->move($finalPath, $fileName);
        return response()->json([
            'path' => $filePath,
            'name' => $fileName,
            'mime_type' => $mime,
            'full_path' => $finalPath . $fileName, // 🔥 ruta completa real
            'expires_at' => now()->addHours(3)->toDateTimeString()
        ]);
    }

    // 🔽 Esta es la nueva función reutilizable que puedes usar en otros controladores
    public static function obtenerArchivoSubido(Request $request, string $campo = 'archivo'): ?UploadedFile
    {
        $receiver = new FileReceiver($campo, $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            return $save->getFile();
        }

        return null;
    }
    public function eliminarArchivoTemporal(Request $request)
    {
        $ruta = $request->input('ruta');
        $nombre = $request->input('nombre');

        if (!$ruta || !$nombre) {
            return response()->json(['mensaje' => 'Faltan datos para eliminar el archivo.'], 400);
        }

        $rutaCompleta = storage_path("app/" . $ruta . $nombre);

        if (File::exists($rutaCompleta)) {
            File::delete($rutaCompleta);
            return response()->json(['mensaje' => 'Archivo temporal eliminado correctamente.']);
        }

        return response()->json(['mensaje' => 'El archivo no existe o ya fue eliminado.']);
    }
}

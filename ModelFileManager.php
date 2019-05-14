<?php
namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait ModelFileManager
{

    /**
     * Main function used to delete a file.
     *
     * @param string $field
     * @return void
     */
    public function deleteTheFile(string $field)
    {
        $this->deleteFile($field);
        $this->save();
        return;
    }

    /**
     * Main function used to store a new file.
     *
     * @param string $type
     * @param string $field
     * @param [type] $file
     * @return void
     */
    public function storeTheFile(string $type = 'store', string $field = 'image', $file = '')
    {
        $this->deleteFile($field);
        $this->storeFile($type,$field,$file);
        $this->save();
        return;
    }

    /**
     * If existe Return the file content.
     *
     * @param string $field
     * @return
     */
    public function getFile(string $field = 'image')
    {
        if (Storage::disk($this->disk)->has($this->{$field})) {
            return Storage::disk($this->disk)->get($this->{$field});
        }
        $this->logError($field, 'Retrieve the contents(get)', 'getFile');
    }

    /**
     * If exist return a download response.
     *
     * @param string $field
     * @return void
     */
    public function downloadFile(string $field = 'image')
    {
        if (Storage::disk($this->disk)->has($this->{$field})) {
            return Storage::disk($this->disk)->download($this->{$field});
        }
        $this->logError($field, 'Download', 'downloadFile');
    }

    /**
     * If exist, delete the file.
     *
     * @param string $field
     * @return void
     */
    public function deleteFile(string $field = 'image')
    {
        //First upload there is no need to delete
        Log::debug($this->{$field});
        if (!$this->{$field}) {
            return;
        }
        if (Storage::disk($this->disk)->has($this->{$field})) {
            Storage::disk($this->disk)->delete($this->{$field});
            $this->{$field} = null;
            return;
        }
        $this->logError($field, 'Delete', 'deleteFile');
    }

    /**
     * Store the content of the file in the disk.
     *
     * @param [type] $file
     * @param string $field
     * @return void
     */
    public function storeFile(string $type = 'store', string $field = 'image', $file = '')
    {
        switch ($type) {
            case 'put':
                $this->contentFile($file, $field);
                break;
            case 'store':
                $this->requestFile($field);
                break;
            default:
                break;
        }
    }

    /**
     * Store the file that came from get or file_get_contents.
     *
     * @param [type] $file
     * @param string $field
     * @return void
     */
    public function contentFile($file, string $field)
    {
        $this->{$field} = Storage::disk($this->disk)->put('', $file);
    }

    /**
     * Store the file that came from the request.
     *
     * @param string $field
     * @return void
     */
    public function requestFile(string $field)
    {
        $request        = request();
        $this->{$field} = $request->{$field}->store('', $this->disk);
    }

    /**
     * Local function used to store errors from the trait.
     *
     * @param string $field
     * @param string $action
     * @param string $funcion
     * @return void
     */
    public function logError(string $field, string $action = 'descargar', string $funcion = ''): void
    {
        $modelo = class_basename($this);
        if (\Auth::check()) {
            Log::error("Error en $funcion, Usuario id = " . \Auth::user()->id . " - Intento $action un archivo del campo $field del modelo $modelo con id = $this->id .");
        } else {
            Log::error("Error en $funcion, Usuario visitante - Intento $action un archivo del campo $field del modelo $modelo con id = $this->id .");
        }
        abort(404);
    }
}

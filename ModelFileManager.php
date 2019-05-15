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
     * @param string $field
     * @param [type] $file
     * @return void
     */
    public function storeTheFile(string $field = 'image', $file = '')
    {
        $this->deleteFile($field);
        $this->storeFile($field, $file);
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
    protected function deleteFile(string $field = 'image')
    {
        //First upload there is no need to delete
        if (!$this->{$field}) {
            return;
        }
        if (Storage::disk($this->disk)->has($this->{$field})) {
            Storage::disk($this->disk)->delete($this->{$field});
            $this->{$field} = null;
            return;
        }
        //If there is not file, just put it in null and return error.
        $this->{$field} = null;
        $this->save();
        $this->logError($field, 'Delete', 'deleteFile');
    }

    /**
     * Store the content of the file in the disk.
     *
     * @param string $field
     * @return void
     */
    protected function storeFile(string $field, $file)
    {
        if ($file) {
            $this->contentFile($file, $field);
        } else {
            $this->requestFile($field);
        }
    }

    /**
     * Store the file that came from get or file_get_contents.
     *
     * @param [type] $file
     * @param string $field
     * @return void
     */
    protected function contentFile($file, string $field)
    {
        $this->{$field} = Storage::disk($this->disk)->put('', $file);
    }

    /**
     * Store the file that came from the request.
     *
     * @param string $field
     * @return void
     */
    protected function requestFile(string $field)
    {
        $request = request();
        if ($this->fileIsSafe($field, $request)) {
            $this->{$field} = $request->{$field}->store('', $this->disk);
            return;
        }
        return;
    }

    /**
     * Check if the file is in the request and came alright.
     *
     * @param string $field
     * @param Request $request
     * @return void
     */
    protected function fileIsSafe(string $field, \Illuminate\Http\Request $request)
    {
        if ($request->hasFile($field) && $request->file($field)->isValid()) {
            return true;
        }
        return false;
    }

    /**
     * Local function used to store errors from the trait.
     *
     * @param string $field
     * @param string $action
     * @param string $funcion
     * @return void
     */
    protected function logError(string $field, string $action = 'descargar', string $funcion = ''): void
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

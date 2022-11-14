<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Storage;

trait FileManager
{

    /**
     * Create or Replace file
     *
     * @param Request $request
     * @param String $storage
     * @param String $fieldName
     * @param String $documentPrefix
     * @param Object $modelData
     * @return mixed Response
     */
    function createOrReplaceFile($file = null, $storage = null, $fieldName = null, $documentPrefix = null, $modelData = null)
    {
        if(!empty($storage) && !empty($fieldName))
        {
            $fileName =  $documentPrefix . md5(time()) . '_' . str_replace(" ", "_", $file->getClientOriginalName());
            try {
                if (Storage::disk($storage)->put($fileName, file_get_contents($file))) {
                    if (!empty($modelData) && isset($modelData->$fieldName)) {
                        if (Storage::disk($storage)->exists($modelData->$fieldName)) {
                            Storage::disk($storage)->delete($modelData->$fieldName);
                        }
                    }
                    return $fileName;
                }
            }
            catch (Exception $e) {
                print_r($e->getMessage());
                die;
            }
        }

        return false;
    }

    /**
     * Remove file
     *
     * @param String $storage
     * @param String $fieldName
     * @param Object $modelData
     * @return mixed Response
     */
    function removeFile($storage = null, $fieldName = null, $modelData = null)
    {
        try {
            if (!empty($storage) && !empty($modelData) && isset($modelData->$fieldName)) {
                if (Storage::disk($storage)->exists($modelData->$fieldName)) {
                    Storage::disk($storage)->delete($modelData->$fieldName);
                    return true;
                }
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
            die;
        }
        return false;
    }

}

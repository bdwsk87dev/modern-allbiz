<?php

namespace App\Http\Traits;
use Illuminate\Support\Facades\Crypt;

trait Encryptable
{
    public function getCryptAttribute($key)
    {
        $value = parent::getAttribute($key);
        if (in_array($key, $this->encryptable)) {
            $value = Crypt::decryptString($value);
        }
        return $value;
    }

    public function setCryptAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable)) {
            $value = Crypt::encryptString($value);
        }
        return parent::setAttribute($key, $value);
    }

    public function getDecryptAll(){
        $attributes = parent::getAttributes();
        foreach ($attributes as $attKey => &$attValue){
            $attValue = ($attValue=="") ? '' : $this->getCryptAttribute($attKey);
        }
        return $attributes;
    }

}

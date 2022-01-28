<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\TaskLogController;
use App\Http\Controllers\DailyTasksLogController;

trait Helper
{
    /**
     * @param $data
     */
    public function createLog($data)
    {
        // var_dump($data);
        $log = new TaskLogController();
        $log->store($data);
    }

    /**
     * @param $data
     */
    public function createDailyLog($data)
    {
        $log = new DailyTasksLogController();
        $log->store($data);
    }

    // Working with decrypt

    /**
     * @param $item
     * @return string
     */
    public function decryptString($item)
    {
        return self::decrypt($item);
    }

    /**
     * @param $item
     * @return mixed
     */
    public function encryptString($item)
    {
        return self::encrypt($item);
    }

    /**
     * @param $item
     * @return string
     */
    protected function decrypt($item)
    {
        try {
            return Crypt::decryptString($item);
        } catch (\RuntimeException $e) {
            return $item;
        }
    }

    protected function encrypt($item)
    {
        return Crypt::encryptString($item);
    }


}

<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;

class BaseRequest extends Request
{
    /**
     * Get clients IP address
     *
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ip();
    }

    /**
     * Get url path
     */
    public function getPath(): string
    {
        return $this->path();
    }
}
<?php

namespace WFub\Enums;

class Failure
{
    public const ERR_PARAM    = 'Invalid parameter specified';
    public const ERR_TYPE     = 'Invalid type specified';
    public const ERR_REQUIRED = 'Required parameters not passed';
    public const ERR_CATALOG  = 'The directory is not available';
    public const ERR_IMAGE    = 'Achievement was not found in the list';
}
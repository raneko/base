<?php

namespace Raneko\Extend\Eloquent;

/**
 * @author Harry Lesmana <harrylesmana@singpost.com>
 */
class Model extends \Illuminate\Database\Eloquent\Model
{
    const CREATED_AT = "sys_created_on";
    const UPDATED_AT = "sys_modified_on";

    public function getCreatedAtColumn()
    {
        return "sys_created_on";
    }

    public function getUpdatedAtColumn()
    {
        return "sys_modified_on";
    }

}

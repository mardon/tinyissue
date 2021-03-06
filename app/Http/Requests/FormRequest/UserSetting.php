<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tinyissue\Http\Requests\FormRequest;

/**
 * UserSetting is a Form Request class for managing add/edit user setting submission (validating, redirect, response, ...)
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class UserSetting extends User
{
    protected $formClassName = 'Tinyissue\Form\UserSetting';

    protected function getRedirectUrl()
    {
        return 'user/settings';
    }
}

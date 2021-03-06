<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tinyissue\Form;

use Tinyissue\Model\Role;

/**
 * User is a class to defines fields & rules for add/edit user form
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class User extends FormAbstract
{
    public function actions()
    {
        if ($this->isEditing()) {
            return [
                'submit' => 'update',
            ];
        }

        return ['submit' => 'add_user'];
    }

    public function fields()
    {
        $fields = [
            'firstname' => [
                'type'  => 'text',
                'label' => 'first_name',
            ],
            'lastname'  => [
                'type'  => 'text',
                'label' => 'last_name',
            ],
            'email'     => [
                'type'  => 'text',
                'label' => 'email',
            ],
        ];

        $fields += $this->innerFields();

        return $fields;
    }

    /**
     * Return password fields
     *
     * @return array
     */
    protected function passwordFields()
    {
        $fields = [];
        $fields['only_complete_if_changing_password'] = [
            'type' => 'legend',
        ];
        $fields['password'] = [
            'type'  => 'password',
            'label' => 'new_password',
        ];
        $fields['password_confirmation'] = [
            'type'  => 'password',
            'label' => 'confirm',
        ];

        return $fields;
    }

    /**
     * For sub-classes to add extra fields or remove fields
     *
     * @return array
     */
    protected function innerFields()
    {
        $fields = [
            'role_id' => [
                'type'    => 'select',
                'label'   => 'role',
                'options' => Role::dropdown(),
            ],
        ];

        if ($this->isEditing()) {
            $fields += $this->passwordFields();
        }

        return $fields;
    }

    public function rules()
    {
        $rules = [
            'firstname' => 'required|max:50',
            'lastname'  => 'required|max:50',
            'email'     => 'required|email',
        ];

        if ($this->isEditing()) {
            $rules['email'] .= '|unique:users,email,'.$this->getModel()->id;
            $rules['password'] = 'confirmed';
        } else {
            $rules['email'] .= '|unique:users,email';
        }

        return $rules;
    }

    public function getRedirectUrl()
    {
        if ($this->isEditing()) {
            return 'administration/users/edit/'.$this->getModel()->id;
        }

        return 'administration/users/add/';
    }
}

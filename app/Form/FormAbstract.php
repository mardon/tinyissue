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

use Illuminate\Database\Eloquent\Model;

/**
 * FormAbstract is an abstract class for Form classes
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
abstract class FormAbstract implements FormInterface
{
    /**
     * An instance of Model
     *
     * @var Model
     */
    protected $model;

    /**
     * Set an instance of model currently being edited
     *
     * @param Model $model
     *
     * @return void|FormInterface
     */
    public function editingModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Setup the object from the route parameters
     *
     * @param array $params
     *
     * @return FormInterface
     */
    public function setup(array $params)
    {
        $model = array_first($params, function ($key, $value) {
            return $value instanceof Model;
        });
        if ($model) {
            $this->editingModel($model);
        }

        return $this;
    }

    /**
     * Whether or not the form is in editing of a model
     *
     * @return boolean
     */
    public function isEditing()
    {
        return $this->model instanceof Model;
    }

    /**
     * Return an instance of the model being edited
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Returns form type
     *
     * @return string
     */
    public function openType()
    {
        return 'open';
    }

    /**
     * Returns an array of form actions
     *
     * @return array
     */
    public function actions()
    {
        return [];
    }

    /**
     * Returns an array of form fields
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }

    /**
     * Returns an array form rules
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Returns the form redirect url on error
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return '';
    }
}

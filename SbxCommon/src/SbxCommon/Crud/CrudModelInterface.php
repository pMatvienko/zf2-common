<?php
namespace SbxCommon\Crud;

interface CrudModelInterface
{
    public function findById($id);

    public function getGrid();

    public function getForm($entity=null);

    public function saveForm($form, $entity=null);

    public function removeEntity($entity);
}
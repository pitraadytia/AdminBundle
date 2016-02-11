<?php

namespace Symfonian\Indonesia\AdminBundle\Handler;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfonian\Indonesia\CoreBundle\Toolkit\Util\StringUtil\CamelCasizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadHandler
{
    private $dirPath;
    private $fields = array();

    public function setUploadDir($dirPath)
    {
        $this->dirPath = $dirPath;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function isUploadable()
    {
        if (empty($this->fields)) {
            return false;
        }

        return true;
    }

    public function upload(EntityInterface $entity)
    {
        if (!is_dir($this->dirPath)) {
            mkdir($this->dirPath);
        }

        $file = null;
        foreach ($this->fields as $field) {
            $getter = CamelCasizer::underScoretToCamelCase('get_'.$field);
            if (method_exists($entity, $getter)) {
                /** @var UploadedFile $file */
                $file = call_user_func_array(array($entity, $getter), array());
            }

            if ($file instanceof UploadedFile) {
                $fileName = sha1(uniqid('SIAB_', true)).'.'.$file->getClientOriginalExtension();

                if (!$file->isExecutable()) {
                    $file->move($this->dirPath, $fileName);
                }

                $setter = CamelCasizer::underScoretToCamelCase('set_'.$field);
                if (method_exists($entity, $setter)) {
                    call_user_func_array(array($entity, $setter), array($fileName));
                }
            }
        }
    }
}

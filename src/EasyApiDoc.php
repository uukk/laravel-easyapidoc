<?php

/**
 * EasyApiDoc for laravel
 * @author: xinran
 * @email: xinranmi@gmail.com
 * @created: 2016-10-21 10:14
 * @updated: 2016-10-21 10:14
 * @logs:
 */

namespace XinRan;

use View;

class EasyApiDoc
{
    public $path = [];
    private $class = [];
    public $api = [];

    public function __construct($filePath = [])
    {
        if (!$filePath) {

            $controllerPath = app_path('Http/Controllers');
            $namespace = 'App\\Http\\Controllers';
            $files = scandir($controllerPath);

            $filePath = [
                [
                    'path'      => $controllerPath,
                    'namespace' => $namespace
                ]
            ];

            foreach ($files as $file) {
                if (!in_array($file, ['.', '..']) && is_dir($controllerPath . '/' . $file)) {
                    $filePath[] = [
                        'path'      => $controllerPath . '/' . $file,
                        'namespace' => $namespace . '\\' . $file,
                    ];
                }
            }
        }

        $this->path = $filePath;
    }

    public function getApiDoc($type = 1)
    {
        $api = $this->getApi($type);
        if (is_string($api)) {
            return $api;
        }

        $group = \Input::get('group', 'group');

        if (!array_has($api, $group)) {
            $group = current(array_keys($api));
        }

        $view = __DIR__ . '/views/api.blade.php';

        return View::file($view, ['apis' => $api, 'group' => $group]);
    }

    public function getApi($type = 1)
    {
        $this->class = $this->getAllPathFile();

        if (!$this->class) {
            return '未获取到相关类文件';
        }

        foreach ($this->class as $one) {
            $class = $this->getInstance($one);
            if (is_string($class)) {
                continue;
            }

            $this->getFunctionDoc($class, $type);
        }

        return $this->api;
    }

    /**
     * 获取传入的文件路径对应的每个类
     * @return array 带命名空间的类名
     */
    public function getAllPathFile()
    {
        foreach ($this->path as $key => $one) {

            if (isset($one['path']) && is_dir($one['path'])) {
                // 解析对应路径内所有文件夹下文件,文件夹暂不重复
                if ($dh = opendir($one['path'])) {
                    while (($file = readdir($dh)) !== false) {
                        if ($file !== '.' && $file !== '..' && is_file($one['path'] . '/' . $file)) {
                            $this->class[] = $one['namespace'] . '\\' . basename($file, '.php');
                        }
                    }
                    closedir($dh);
                }
            } elseif (isset($one['path']) && is_file($one['path'])) {
                $this->class[] = $one['namespace'] . '\\' . basename($one['path'], '.php');
            }
        }

        return $this->class ? array_unique($this->class) : [];
    }

    /**
     * 获取反射类
     * @param string $class
     * @return bool|\ReflectionClass|string
     */
    private function getInstance($class = '')
    {
        try {
            $class = new \ReflectionClass($class);
            return $class;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 获取一个类中方法对应的注释
     * @param class $instance 反射类
     * @param int $type 1 公共方法，2表示公共和保护 3表示全部
     * @return bool
     */
    private function getFunctionDoc($instance = null, $type = 1)
    {
        if ($type == 1) {
            $methods = $instance->getMethods(\ReflectionMethod::IS_PUBLIC);
        } elseif ($type == 2) {
            $methods = $instance->getMethods(\ReflectionMethod::IS_PUBLIC + \ReflectionMethod::IS_PROTECTED);
        } else {
            $methods = $instance->getMethods(
                \ReflectionMethod::IS_PUBLIC
                + \ReflectionMethod::IS_PROTECTED
                + \ReflectionMethod::IS_PRIVATE
            );
        }

        if (!$methods) {
            return false;
        }

        foreach ($methods as $method) {
            if (!($apiRes = $this->analysis($method))) {
                continue;
            }
            $this->api[$apiRes['group']][] = $apiRes;
        }
    }


    /**
     * 解析一个注释块并加入到文档数组中
     * @param $method
     * @return array|bool
     */
    private function analysis($method)
    {
        $doc = $method->getDocComment();

        // 匹配不同参数
        $has = preg_match_all('/@([\S]{1,})\s([\S]{1,})\s([\S]{0,})\s([\S]{0,})/', $doc, $result);

        if (!$has) {
            return false;
        }

        if (!in_array('route', $result[1])) {
            return false;
        }

        $route = '';
        $group = 'group';
        $name = '';
        $description = '';
        $return = [];
        $params = [];
        $method = '';
        $created = '';
        $updated = '';

        foreach ($result[1] as $key => $label) {
            if ($label == 'group') {
                $group = $result[2][$key];
            }

            if ($label == 'route') {
                $route = $result[2][$key];
            }

            if ($label == 'method') {
                $method = $result[2][$key];
            }

            if ($label == 'description') {
                $description = $result[2][$key];
            }

            if ($label == 'created') {
                $created = $result[2][$key] . ' ' . $result[3][$key];
            }

            if ($label == 'updated') {
                $updated = $result[2][$key] . ' ' . $result[3][$key];
            }

            if ($label == 'name') {
                $name = $result[2][$key];
            }

            if ($label == 'param') {
                $paramType = $result[2][$key];
                $paramArg = $result[3][$key];
                $paramDesc = $result[4][$key];
                if (!$paramArg) {
                    continue;
                }

                $paramArg = explode('.', $paramArg);
                $params = $this->analysisParam($paramArg, $paramType, $paramDesc, $params);
            }

            if ($label == 'return') {
                $returnType = $result[2][$key];
                $returnArg = $result[3][$key];
                $returnDesc = $result[4][$key];
                if (!$returnArg) {
                    continue;
                }

                $returnArg = explode('.', $returnArg);
                $return = $this->analysisParam($returnArg, $returnType, $returnDesc, $return);
            }
        }

        $data = [
            'group'       => $group,
            'route'       => $route,
            'method'      => $method,
            'name'        => $name,
            'description' => $description,
            'created'     => $created,
            'updated'     => $updated,
            'returns'     => $return,
            'args'        => $params,
        ];

        return $data;
    }

    /**
     * 递归设置参数属性
     * @param $paramArg
     * @param $type
     * @param $description
     * @param $result
     * @return array
     */
    public function analysisParam($paramArg, $type, $description, $result)
    {
        $name = array_shift($paramArg);
        if (substr($name, 0, 1) == '$') {
            $name = substr($name, 1);
        }

        if (count($paramArg)) {
            $result[$name]['detail'] = $this->analysisParam($paramArg, $type, $description, isset($result[$name]['detail']) ? $result[$name]['detail'] : []);
        } else {
            $result[$name] = ['type' => $type, 'description' => $description];
        }

        return $result;
    }
}
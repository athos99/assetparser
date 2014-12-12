<?php
namespace athos99\assetparser;
use Yii;
use yii\caching\FileCache;



class Less extends Parser
{

    public $auto = true;
    public $max_nesting_level = 200;




    /**
     * Parse a Less file to CSS
     *
     *
     * @param string $src   source file path + name
     * @param string $dst   destination file path + name
     * @param array $options  options
     *                      'auto' : auto dependency
     *                      'max_nesting_level' : xdebug max_nesting_level
     * @return bool
     * @throws Exception
     */
    public function parse($src, $dst, $options)
    {
        if (YII_ENV_DEV) {
            Yii::trace("Converted less $src into $dst ", __METHOD__);
            Yii::beginProfile("Converted less $src into $dst ", __METHOD__);
        }

        $update = false;
        $this->max_nesting_level = isset($options['max_nesting_level']) ? $options['max_nesting_level'] : $this->max_nesting_level;
        $max_nesting_level =ini_get('xdebug.max_nesting_level');
        if ($max_nesting_level !== false && !empty($this->max_nesting_level) ) {
            ini_set('xdebug.max_nesting_level', $this->max_nesting_level);
        }
        $this->auto = isset($options['auto']) ? $options['auto'] : $this->auto;
        try {
            if ($this->auto) {
                /* @var FileCache $cacheMgr */
                $cacheMgr = Yii::createObject('yii\caching\FileCache');
                $cacheMgr->init();
                $cacheId = 'less#' . $dst;
                $cache = $cacheMgr->get($cacheId);
                if ($cache === false || (@filemtime($dst) < @filemtime($src))) {
                    $cache = $src;
                }
                $less = new \lessc();
                $newCache = $less->cachedCompile($cache);

                if (!is_array($cache) || ($newCache["updated"] > $cache["updated"])) {
                    $cacheMgr->set($cacheId, $newCache);
                    file_put_contents($dst, $newCache['compiled']);
                    $update =true;
                }
            } else {
                $less = new \lessc();
                $less->compileFile($src, $dst);
                $update =true;
            }
        } catch (exception $e) {
            throw new Exception(__CLASS__ . ': Failed to compile less file : ' . $e->getMessage() . '.');
        }
        if (YII_ENV_DEV) {
            Yii::endProfile("Converted less $src into $dst ", __METHOD__);
        }

        return $update;
    }
}
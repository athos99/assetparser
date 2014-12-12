<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2011 Michel Bobillier Aka Athos99
 * @license GNU General Public License v3 http://www.gnu.org/licenses/gpl.html
 * @version 1.0.0 (2011-05-05)
 */

namespace athos99\assetparser;

use Yii;
use yii\base\Component;
use yii\web\AssetConverterInterface;

class Converter extends Component implements AssetConverterInterface
{
    /**
     * @var array parsers
     */
    public $parsers = [
        'sass' => [ // file extension to parse
            'class' => 'athos99\assetparser\Sass',
            'output' => 'css', // parsed output file type
            'options' => [
                'cachePath' => '@app/runtime/cache/sass-parser' // optional options
            ],
        ],
        'scss' => [ // file extension to parse
            'class' => 'athos99\assetparser\Sass',
            'output' => 'css', // parsed output file type
            'options' => [] // optional options
        ],
        'less' => [ // file extension to parse
            'class' => 'athos99\assetparser\Less',
            'output' => 'css', // parsed output file type
            'options' => [
                'auto' => true // optional options
            ]
        ]
    ];


    /**
     * @var boolean if true the asset will always be published
     */
    public $force = false;


    /**
     * Converts a given asset file into a CSS or JS file.
     * @param string $asset the asset file path, relative to $basePath
     * @param string $basePath the directory the $asset is relative to.
     * @return string the converted asset file path, relative to $basePath.
     */
    public function convert($asset, $basePath)
    {
        $pos = strrpos($asset, '.');
        if ($pos !== false) {
            $ext = substr($asset, $pos + 1);
            if (isset($this->parsers[$ext])) {
                $parserConfig = $this->parsers[$ext];
                $result = substr($asset, 0, $pos + 1) . $parserConfig['output'];
                if ($this->force || !is_file($basePath . '/' . $result) || (@filemtime("$basePath/$result") < filemtime("$basePath/$asset"))) {
                    if (YII_ENV_DEV) {
                        Yii::info("Converted $asset into $result ", __METHOD__);
                        Yii::beginProfile("Converted $asset into $result ", __METHOD__);
                    }
                    $parser = new $parserConfig['class']($parserConfig['options']);
                    $parser->parse("$basePath/$asset", "$basePath/$result", isset($parserConfig['options']) ? $parserConfig['options'] : []);
                    if (YII_ENV_DEV) {
                        Yii::endProfile("Converted $asset into $result ", __METHOD__);
                    }
                }
                return $result;

            }
        }
        return $asset;
    }
}

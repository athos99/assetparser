<?php
namespace app\extensions\assetparser;
use Yii;

class Sass extends Parser
{

    /**
     * @var string to the class pointing to where sass parser is located.
     */
    public $sassParserPath = '@app/extensions/assetparser/vendors/phamlp/sass/SassParser.php';

    /**
     * @var string to the sass parser cache
     */

    public $cachePath = '@app/runtime/cache/sass-parser';

    /**
     * Parse a Sass file to CSS
     */
    public function parse($src, $dst, $options)
    {
        require_once(Yii::getAlias($this->sassParserPath));
        if (!empty($options['cachePath'])) {
            $options['cache_location'] = Yii::getAlias($options['cachePath']);

            if (!is_dir($options['cache_location'])) {
                mkdir($options['cache_location'], 0777, true);
            }
        }

        $parser = Yii::createObject('SassParser', $options);
        file_put_contents($dst, $parser->toCss($src));
    }
}
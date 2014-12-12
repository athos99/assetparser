<?php
namespace athos99\assetparser;

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
        if (YII_ENV_DEV) {
            Yii::trace("Converted sass $src into $dst ", __METHOD__);
            Yii::beginProfile("Converted sass $src into $dst ", __METHOD__);
        }

        require_once(Yii::getAlias($this->sassParserPath));
        if (!empty($options['cachePath'])) {
            $options['cache_location'] = Yii::getAlias($options['cachePath']);

            if (!is_dir($options['cache_location'])) {
                mkdir($options['cache_location'], 0777, true);
            }
        }

        $parser = Yii::createObject('SassParser', $options);
        file_put_contents($dst, $parser->toCss($src));
        if (YII_ENV_DEV) {
            Yii::endProfile("Converted sass $src into $dst ", __METHOD__);
        }

    }
}
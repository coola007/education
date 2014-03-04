<?php

/**
 * 验证码
 *
 * 注意: 出于保存验证码 Key/Code 的需要
 * 会生成 __Ku_Captcha:key 这样的 Hash 键
 *
 * 把数据保存到Redis中, 主要防止被人刷太多验证码时,
 * Session要一次性加载太多太多数据... 这会给PHP本身带来很大问题
 *
 */
class Coola_CaptchaImage {

    /**
     * 验证码字符集
     *
     * 去掉了 0,1,9,o,l,q,i 这几个不好识别的字符
     *
     * @var array
     */
    protected $char = array(
        '2', '3', '4', '5', '6', '7', '8',
        'a', 'b', 'c', 'd', 'e', 'f', 'g',
        'h', 'j', 'k', 'm', 'n',
        'p', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        'A', 'B', 'C', 'D', 'E', 'F', 'G',
        'H', 'J', 'K', 'L', 'M', 'N',
        'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    );

    /**
     * 验证码的字符数
     *
     * @var int
     */
    protected $randlen  = 4;
    protected $imageRes = null;

    /**
     * 干扰因素
     *
     * @var array
     */
    protected $interfere = array('line', 'pixel', 'arc');

    /**
     * 用于匹配不同的验证码的Key
     *
     * @var string
     */
    protected $captchaKey = '';

    /**
     * 验证码
     *
     * @var string
     */
    protected $captchaCode = '';

    /**
     * ttf字体文件的目录
     *
     * @var string
     */
    protected $fontDirectory = '/';

    /**
     * 输出图片的宽度
     *
     * @var int
     */
    protected $width = 122;

    /**
     * 输出图片的高度
     *
     * @var int
     */
    protected $height = 50;

    /**
     * 验证码字体集合
     *
     * @var array
     */
    protected $fonts = array('AntykwaBold', 'Duality', 'Jura', 'StayPuft', 'VeraSansBold');

    /**
     * 验证码字体颜色
     *
     * @var array
     */
    protected $fontColors = array(
        array(0x00, 0x00, 0x00), // black
        array(0x28, 0x28, 0xFF), // blue
        array(0x00, 0xA6, 0x00), // green
        array(0xCE, 0x00, 0x00)  // red
    );

    /**
     * 背景色
     *
     * @var array
     */
    protected $bgColor = array('R' => 0xFF, 'G' => 0xFF, 'B' => 0xFF);

    /**
     * 用于保存验证信息的Redis对象
     *
     * @var Redis
     */
    protected $_redis = null;

    /**
     * @todo 以后可以考虑把数据保存到Redis中, 防止被人刷太多验证码时, Session要一次性加载太多数据
     */
    public function __construct($key = '') {
        $this->_redis = Registry::get('redis');

        $key = trim($key);
        if ($key) {
            $this->setKey($key);
        }
        else {
            $this->resetKey();
            $this->_initCode();
        }

        $this->setFontDirectory(rtrim(\APPLICATION_PATH, DIRECTORY_SEPARATOR) . '/data/fonts');
    }

    /**
     * 在退出时, 保存一些验证码的配置
     */
    public function __destruct() {
        $this->_save();
    }

    /**
     * 保存验证的一些配置到 Session 中
     */
    public function _save() {
        $key   = '__Ku_Captcha:' . $this->captchaKey;
        $value = array(
            'code'     => $this->captchaCode,
            'height'   => $this->height,
            'width'    => $this->width,
            'bgColor'  => $this->bgColor,
            'interfer' => $this->interfere,
            'len'      => $this->randlen,
        );

        // 数据只保留20分钟
        $this->_redis->setex($key, 1200, serialize($value));
    }

    /**
     * 通过 Key 加载验证码的配置
     */
    public function _load() {
        $key = '__Ku_Captcha:' . $this->captchaKey;

        // 默认配置
        $def = array(
            'code'     => $this->captchaCode,
            'height'   => $this->height,
            'width'    => $this->width,
            'bgColor'  => $this->bgColor,
            'interfer' => $this->interfere,
            'len'      => $this->randlen,
        );

        $value = unserialize($this->_redis->get($key));

        if (is_array($value)) {
            $value = array_merge($def, $value);

            $this->captchaCode = $value['code'];
            $this->setHeight($value['height']);
            $this->setWidth($value['width']);
            $this->setBgColor($value['bgColor']['R'], $value['bgColor']['G'], $value['bgColor']['B']);
            $this->setInterfere($value['interfer']);
            $this->setLen($value['len']);
        }

        return $value;
    }

    /**
     * 取得Redis对象
     *
     * @return Redis
     */
    protected function getRedis() {
        return $this->_redis;
    }

    /**
     * 设置Redis对象
     *
     * @param \Ku\Redis $redis  Redis对象
     * @return \Ku\Captcha
     */
    public function setRedis(Redis $redis) {
        $this->_redis = $redis;
        return $this;
    }

    /**
     * 初始化验证码的Key
     */
    public function resetKey() {
        $this->captchaKey = md5(__CLASS__ . '_' . time() . '_' . mt_rand(10000, 99999));
    }

    /**
     * 初始化验证码的Code
     */
    protected function _initCode() {
        $this->captchaCode = $this->randChar();
    }

    /**
     * 验证用户的输出是否与验证码相符, 不区分大小写
     *
     * @param array $value  所要验证的验证码, array('key'=>..., 'code'=>...)
     * @return boolean
     */
    public function isValid($value) {
        if (!is_array($value)) {
            return false;
        }

        if (!isset($value['key']) or !isset($value['code'])) {
            return false;
        }

        $this->setKey($value['key']);

        $code = '';
        foreach ($this->captchaCode as $i) {
            $code .= $i;
        }

        return strcasecmp($code, $value['code']) === 0 ? true : false;
    }

    /**
     * 获取验证码字符串长度
     *
     * @return int
     */
    public function getLen() {
        return $this->randlen;
    }

    /**
     * 设置 验证码字符串长度
     *
     * @param string $len   验证码字符串长度
     * @return \Ku\Captcha
     */
    public function setLen($len = 4) {
        $this->randlen = abs(intval($len));
        return $this;
    }

    /**
     * 设置验证码的Key
     *
     * @param string $key   验证码的Key
     * @return \Ku\Captcha
     */
    public function setKey($key) {
        $this->captchaKey = (string) $key;

        // 重新加载配置
        $this->_load();

        return $this;
    }

    /**
     * 验证码的Key
     *
     * @return string
     */
    public function getKey() {
        return $this->captchaKey;
    }

    /**
     * 设置 ttf字体的目录
     *
     * @param string $fontDirectory
     *
     * @return \Ku\Captcha
     */
    public function setFontDirectory($fontDirectory = \DIRECTORY_SEPARATOR) {
        $this->fontDirectory = rtrim($fontDirectory, \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * ttf字体的目录
     *
     * @return string
     */
    public function getFontDirectory() {
        return $this->fontDirectory;
    }

    /**
     * 设置图像宽度
     *
     * @param int $w
     */
    public function setWidth($w) {
        $this->width = min(abs(intval($w)), 200);
        return $this;
    }

    /**
     * 设置图像高度
     *
     * @param int $h
     */
    public function setHeight($h) {
        $this->height = min(abs(intval($h)), 100);
        return $this;
    }

    /**
     * 设置干扰素类型
     *
     * @param string|array $interfere [line, pixel, arc]
     */
    public function setInterfere($interfere) {
        $this->interfere = $interfere;
    }

    /**
     * 输出图片
     *
     * @param int $len  验证码数量, 最多16
     */
    public function show($len = 4) {
        if ($len > 4) {
            $this->randlen = max(abs(intval($len)), 16);
        }

        // 每次显示时, 重新生成Code
        $this->randChar();

        // 输出 png 声明
        header("Content-type: image/png");

        // 创建图片资源
        if (!$this->imageRes) {
            $this->CreateImageResources();
        }

        $this->write();

        imagepng($this->imageRes);
        imagedestroy($this->imageRes);
    }

    /**
     * 将文字写入图片资源及添加干扰素
     */
    protected function write() {
        $randChar  = $this->captchaCode;
        $randColor = $this->randFontColor();
        $count     = count($randChar);
        $pWvalue   = round(($this->width - 2) / $count);

        if ($randColor != -1) {
            $interfere = $this->interfere;

            if (!is_array($interfere)) {
                $interfere = array((string) $interfere);
            }

            foreach ($interfere as $set) {
                if (in_array($set, array('line', 'pixel', 'arc'))) {
                    if (method_exists($this, $set)) {
                        $this->{$set}($randColor);
                    }
                }
            }
        }

        for ($i = 0; $i < $count; $i++) {
            $size = mt_rand(12, $this->height / 2);
            $agle = mt_rand(-8, 8);
            $maxX = $pWvalue * ($i + 1);
            $minX = $maxX - $pWvalue;

            $x = mt_rand($minX + 1, $maxX - $size);
            $y = mt_rand($size + 1, $size + ((int) ($size / 2) - 2));

            // 在图片在添加文字
            imagettftext($this->imageRes, $size, $agle, $x, $y, $randColor, $this->randFontPath(), $randChar[$i]);
        }
    }

    /**
     * 取得随机字符串
     *
     * @return array
     */
    protected function randChar() {
        $len  = $this->randlen < 4 ? 4 : ( $this->randlen > 10 ? 10 : $this->randlen);
        $max  = count($this->char) - 1;
        $char = array();
        $i    = 0;

        while ($i < $len) {
            $randIndex = mt_rand(0, $max);

            if (isset($this->char[$randIndex])) {
                $char[] = $this->char[$randIndex];
                $i++;
            }
        }

        $this->captchaCode = $char;

        return $char;
    }

    /**
     * 随机字体
     *
     * @return string
     */
    public function randFontPath() {
        return $this->getFontDirectory() . $this->fonts[array_rand($this->fonts)] . '.ttf';
    }

    /**
     * 随机字体顔色
     *
     * @return \GD  int
     */
    protected function randFontColor() {
        static $randFg = array();

        $randIndex = mt_rand(0, count($this->fontColors) - 1);

        if (!isset($randFg[$randIndex])) {
            $randColor          = $this->fontColors[$randIndex];
            $randFg[$randIndex] = imagecolorallocate($this->imageRes, $randColor[0], $randColor[1], $randColor[2]);
        }

        return $randFg[$randIndex];
    }

    /**
     * 点状干扰素
     *
     * @param int $setColor 点的颜色
     * @param int $size     点的数量
     */
    protected function pixel($setColor, $size = 100) {
        $size = ($size > 0) ? (int) $size : 50;
        $hVal = $this->height;
        $wVal = $this->width;

        for ($i = 0; $i < $size; $i++) {
            imagesetpixel($this->imageRes, rand(0, $wVal), rand(0, $hVal), (int) $setColor);
        }
    }

    /**
     * 弧线状干扰素
     *
     * @param int $setColor
     * @param int $size
     */
    protected function arc($setColor, $size = 1) {
        /**
         * @todo 功能未完成
         */
    }

    /**
     * 线条状干扰素
     *
     * @param int $setColor
     * @param int $size
     */
    protected function line($setColor, $size = 1) {
        $size = ($size > 0 && $size < 3) ? (int) $size : 1;
        $hVal = $this->height;
        $wVal = $this->width;

        for ($i = 0; $i < $size; $i++) {
            imageline($this->imageRes, 0, mt_rand(0, $hVal), $wVal, mt_rand(0, $hVal), (int) $setColor);
        }
    }

    /**
     * 设置背景颜色
     *
     * @param int $R
     * @param int $G
     * @param int $B
     */
    public function setBgColor($R = 0xFF, $G = 0xFF, $B = 0xFF) {
        $this->bgColor['R'] = max(min(abs(intval($R)), 255), 0);
        $this->bgColor['G'] = max(min(abs(intval($G)), 255), 0);
        $this->bgColor['B'] = max(min(abs(intval($B)), 255), 0);
    }

    /**
     * 创建图片资源
     *
     * @return \Captcha
     */
    protected function CreateImageResources() {
        $this->imageRes = imagecreatetruecolor($this->width, $this->height);

        $bgColor = imagecolorallocate($this->imageRes, $this->bgColor['R'], $this->bgColor['G'], $this->bgColor['B']);

        imagefilledrectangle($this->imageRes, 0, 0, $this->width, $this->height, $bgColor);
        imagefill($this->imageRes, 0, 0, $bgColor);
    }

}

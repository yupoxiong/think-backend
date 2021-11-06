<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\api\service;


use app\api\exception\ApiServiceException;
use app\common\exception\CommonServiceException;
use app\common\service\DateService;
use app\common\service\StringService;
use think\facade\Env;
use util\jwt\Jwt;
use util\jwt\JwtException;

class TokenService extends ApiBaseService
{
    /** @var string */
    protected $key;

    /** @var string 颁发者 */
    protected $iss;


    /** @var string 使用者 */
    protected $aud;


    /** @var int 默认token过期时间 */
    protected int $exp = 86400000;


    /** @var Jwt */
    protected Jwt $jwt;

    public function __construct($config = null)
    {
        $this->jwt = new Jwt();

        if ($config !== null && is_array($config)) {
            $this->key = $config['key'] ?: $this->key;
            $this->iss = $config['iss'] ?: $this->iss;
            $this->aud = $config['aud'] ?: $this->aud;
            $this->exp = $config['exp'] ?: $this->exp;
        } else {
            $this->key = Env::get('jwt.key');
            $this->iss = Env::get('jwt.iss');
            $this->aud = Env::get('jwt.aud');
            $this->exp = (int)Env::get('jwt.exp') ?: $this->exp;

        }


        if (!$this->key || !$this->iss || !$this->aud) {
            throw new ApiServiceException('请在.env文件中配置jwt信息');
        }
    }

    /**
     * 获取token
     * @param int $uid
     * @param array $claim
     * @return string
     * @throws ApiServiceException
     */
    public function getToken(int $uid, array $claim = []): string
    {
        $time = time();

        try {
            $jti = md5(StringService::getRandString(20) . DateService::microTimestamp());
        } catch (CommonServiceException $e) {
            throw new ApiServiceException($e->getMessage());
        }

        $token = $this->jwt->setKey($this->key)
            //->setClaim('iss', $iss)// 签发者
            //->setClaim('aud', $aud)// 使用者
            //->setClaim('iat', $time)// 签发时间
            //->setClaim('nbf', $time)// 在此时间前不可用
            ->setClaim('exp', $time + $this->exp)// 过期时间
            ->setClaim('jti', $jti)// tokenID
            ->setClaim('uid', $uid);// 用户ID
        // 附加参数
        if (count($claim) > 0) {
            foreach ($claim as $c_key => $c_value) {
                $token = $token->setClaim($c_key, $c_value);
            }
        }

        try {
            return $token->getToken();
        } catch (JwtException $e) {
            throw  new ApiServiceException('生成token失败，信息：' . $e->getMessage());
        }
    }

    /**
     * 需要写完验证token的
     * @param $token
     * @return mixed
     * @throws ApiServiceException
     */
    public function checkToken($token)
    {
        try {
            $check = $this->jwt->setKey($this->key)->checkToken($token);
        } catch (JwtException $e) {
            throw  new ApiServiceException($e->getMessage());
        }
        if ($check) {
            return $this->jwt->getUid();
        }
        throw new ApiServiceException($this->jwt->getMessage());
    }
}
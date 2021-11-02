<?php
/**
 * jwt实现
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);

namespace util\jwt;

use JsonException;

class Jwt
{

    public const ALG = [
        'HS256' => 'sha256',
        'HS384' => 'sha384',
        'HS512' => 'sha521',
        'RS256' => OPENSSL_ALGO_SHA256,
        'RS384' => OPENSSL_ALGO_SHA384,
        'RS512' => OPENSSL_ALGO_SHA512,
    ];

    // 头部
    protected array $header = [
        'alg' => 'HS256',// 生成signature的算法
        'typ' => 'JWT',// 类型
    ];

    /**
     * @var string 签名方式
     */
    protected string $alg = 'HS256';
    // 负载
    protected array $payload = [];
    // 加密key
    protected string $key = '';

    // 错误消息
    protected string $message = '';
    // 私钥
    protected string $privateKey = '';
    // 公钥
    protected string $publicKey = '';
    // token是否已过期
    protected bool $expired;

    /**
     * 设置key
     * @param $key
     * @return $this
     */
    public function setKey($key): Jwt
    {
        $this->key = $key;
        return $this;
    }

    /**
     * 获取header
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * 设置header
     * @param $name
     * @param $value
     * @return $this
     */
    public function setHeader($name, $value): Jwt
    {
        $this->header[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setClaim($name, $value): Jwt
    {
        $this->payload[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getClaim($name)
    {
        return $this->payload[$name];
    }

    /**
     * @param $payload
     * @return Jwt
     */
    public function setPayload($payload): Jwt
    {
        $this->payload = $payload;
        return $this;
    }


    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * 获取jwt的token
     * @return string
     * @throws JwtException
     */
    public function getToken(): string
    {
        $this->checkSignKey();

        $header_str = $this->base64UrlEncode($this->jsonEncode($this->header));

        $payload_str = $this->base64UrlEncode($this->jsonEncode($this->payload));

        return $header_str . '.' . $payload_str . '.' . $this->signature($header_str . '.' . $payload_str);
    }

    /**
     * 检查签名key
     * @throws JwtException
     */
    protected function checkSignKey(): bool
    {
        switch ($this->alg) {
            case 'HS256':
            case 'HS384':
            case 'HS512':
            default:
                if (empty($this->key)) {
                    throw new JwtException('请设置加密key');
                }
                break;
            case 'RS256':
            case 'RS384':
            case 'RS512':
                if (empty($this->privateKey)) {
                    throw new JwtException('请设置私钥');
                }
                break;
        }

        return true;
    }

    /**
     * token是否已过期
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expired;
    }

    /**
     * 检查验证key
     * @throws JwtException
     */
    protected function checkVerifyKey(): bool
    {
        switch ($this->alg) {
            case 'HS256':
            case 'HS384':
            case 'HS512':
            default:
                if (empty($this->key)) {
                    throw new JwtException('请设置加密key');
                }
                break;
            case 'RS256':
            case 'RS384':
            case 'RS512':
                if (empty($this->publicKey)) {
                    throw new JwtException('请设置公钥');
                }
                break;
        }

        return true;
    }


    /**
     * 验证token
     * @param $token
     * @return bool
     * @throws JwtException
     */
    public function checkToken($token): bool
    {
        $array = explode('.', $token);
        if (count($array) !== 3) {
            throw new JwtException('token格式不正确');
        }

        [$header, $payload, $sign] = $array;

        $header_array = $this->jsonDecode($this->base64UrlDecode($header));
        if (!isset($header_array['alg'])) {
            throw new JwtException('未定义签名算法');
        }

        $this->setAlg($header_array['alg']);

        // 签名验证
        $this->checkSign($header, $payload, $sign);

        $this->header = $header_array;

        $payload_array = $this->jsonDecode($this->base64UrlDecode($payload));

        $this->setPayload($payload_array);

        $time = time();

        // 签发时间验证
        if (isset($this->payload['iat']) && $this->payload['iat'] > $time) {
            $this->setMessage('签发时间晚于当前时间');
            return false;
        }

        // 过期时间验证
        if (isset($this->payload['exp']) && $this->payload['exp'] < $time) {
            $this->setExpired(true);
            $this->setMessage('token已过期');
            return false;
        }

        // 未到使用时间验证
        if (isset($this->payload['nbf']) && $this->payload['nbf'] > $time) {
            $this->setMessage('未到使用时间');
            return false;
        }

        return true;

    }

    /**
     * 检查签名
     * @param $header
     * @param $payload
     * @param $sign
     * @return bool
     * @throws JwtException
     */
    public function checkSign($header, $payload, $sign): bool
    {
        switch ($this->alg) {
            case 'HS256':
            case 'HS384':
            case 'HS512':
            default:
                // 直接加密，进行对比即可
                if ($this->signature($header . '.' . $payload) !== $sign) {
                    throw new JwtException('签名验证失败');
                }
                break;
            case 'RS256':
            case 'RS384':
            case 'RS512':
                if (empty($this->publicKey)) {
                    throw new JwtException('请设置公钥');
                }

                if(!$this->verifyRsSign($header . '.' . $payload,$sign)){
                    throw new JwtException('签名验证失败');
                }

                break;
        }

        return true;

    }

    /**
     * 验证签名
     * @param $data
     * @param $sign
     * @return bool
     */
    public function verifyRsSign($data,$sign): bool
    {
        $signature  = $this->base64UrlDecode($sign);
        $publicKey  = openssl_get_publickey($this->getPublicKey());
        $res        = openssl_verify($data, $signature, $publicKey,self::ALG[$this->getAlg()]);
        openssl_free_key($publicKey);

        return $res===1;
    }


    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * 获取当前设置的uid
     * @return mixed
     */
    public function getUid()
    {
        return $this->getClaim('uid');
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }


    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * base64url解码
     * @param string $input
     * @return false|string
     */
    protected function base64UrlDecode(string $input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $append_length = 4 - $remainder;
            $input         .= str_repeat('=', $append_length);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * base64url编码
     * @param string $data
     * @return string
     */
    protected function base64UrlEncode(string $data): string
    {
        return str_replace('=', '', strtr(base64_encode($data), '+/', '-_'));
    }


    /**
     * 转换成json
     * @param mixed $data
     * @return string
     * @throws JwtException
     */
    protected function jsonEncode($data): string
    {
        try {
            return json_encode($data, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new JwtException($e->getMessage());
        }
    }

    /**
     * json转换为数组
     * @param string $data
     * @return mixed
     * @throws JwtException
     */
    protected function jsonDecode(string $data)
    {
        try {
            return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new JwtException($e->getMessage());
        }
    }

    /**
     * 签名
     * @param $input
     * @return string
     */
    protected function signature($input): string
    {
        $alg = $this->getAlg();

        switch ($alg) {
            case 'HS256':
            case 'HS384':
            case 'HS512':
            default:
                $result = $this->base64UrlEncode(hash_hmac(self::ALG[$alg], $input, $this->getKey(), true));
                break;
            case 'RS256':
            case 'RS384':
            case 'RS512':
                $result = openssl_sign($input, $sign, $this->getPrivateKey(), self::ALG[$alg]) ? $this->base64UrlEncode($sign) : '';
                break;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * @param string $privateKey
     * @return Jwt
     */
    public function setPrivateKey(string $privateKey): Jwt
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     * @return Jwt
     */
    public function setPublicKey(string $publicKey): Jwt
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlg(): string
    {
        return $this->alg;
    }


    /**
     * @param string $alg
     * @return Jwt
     */
    public function setAlg(string $alg): Jwt
    {
        $this->alg = $alg;

        $this->header['alg'] = $alg;

        return $this;
    }

    /**
     * 设置当前token的过期状态
     * @param bool $expired
     */
    public function setExpired(bool $expired): void
    {
        $this->expired = $expired;
    }
}
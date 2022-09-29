<?php
namespace Rahulstech\Blogging\Services;

use Klein\Request;
use Klein\AbstractResponse;
use Rahulstech\Blogging\Entities\User;
use Rahulstech\Blogging\DatabaseBootstrap;
use Rahulstech\Blogging\Helpers\AuthToken;
use Klein\ServiceProvider;
use DateTime;
use DateInterval;
use Reflection;
use ReflectionObject;

class AuthService
{
    public const KEY_AUTHTOKEN = "authtoken";

    private ServiceProvider $service;

    private ?Request $request = null;

    private ?AuthToken $authtoken = null;

    private ?AbstractResponse $response = null;

    private ?User $user = null;
    
    public function __construct(ServiceProvider $service)
    {
        $this->service = $service;
    }

    public function getRequest(): Request
    {
        $request = $this->request;
        if (null===$request)
        {
            $request = $this->getNonAccessiblePropertyValueOf($this->service,"request");
            $this->request = $request;
        }
        return $request;
    }

    public function getResponse(): AbstractResponse 
    {
        $response = $this->response;
        if (null===$response)
        {
            $response = $this->getNonAccessiblePropertyValueOf($this->service,"response");
            $this->response = $response;
        }
        return $response;
    }

    public function authenticate(Request $req): bool
    {
        return $this->authenticate_cookie($req);
    }

    public function addAuthToken(User $user, bool $rememberforever=false): bool
    {
        $authtoken = new AuthToken();
        $authtoken->setUserId($user->getUserId());
        if (!$rememberforever)
        {
            $sevendays = new DateInterval("P7D");
            $expire = (new DateTime())->add($sevendays);
            $authtoken->setExpire($expire);
        }
        $req = $this->getRequest();
        $res = $this->getResponse();
        return $this->addAthTokenCookie($req,$res,$authtoken);
    }

    public function removeAuthToken(): bool 
    {
        $res = $this->getResponse();
        return $this->removeAuthTokenCookie($res);
    }

    private function authenticate_cookie(Request $request): bool
    {
        $cookies = $request->cookies();
        $token = $cookies->get(AuthService::KEY_AUTHTOKEN);
        if (null===$token) return false;
        $authtoken = AuthToken::decode($token);
        $user = DatabaseBootstrap::getUserRepo()->find($authtoken->getUserId());
        if (null!==$user)
        {
            $this->authtoken = $authtoken;
            $this->user = $user;
            $this->service->context->put("me",$user);
        }
        return false;
    }

    private function addAthTokenCookie(Request $req, AbstractResponse $res, AuthToken $authtoken): bool 
    {

        $token = $authtoken->encode();
        $res->cookie(AuthService::KEY_AUTHTOKEN,$token,$authtoken->getExpire()->getTimestamp());
        return true;
    }

    private function removeAuthTokenCookie(AbstractResponse $res1): bool 
    {
        $service = $this->service;
        $reflection = new ReflectionObject($service);
        $prop = $reflection->getProperty("response");
        $prop->setAccessible(true);
        $res = $prop->getValue($service);
        
        $res->cookie(AuthService::KEY_AUTHTOKEN,"",0);
        $res->sendCookies();
        $this->service->context->remove("me");
        return true;
    }

    public function getAuthToken(): ?AuthToken
    {
        return $this->authtoken;
    }

    public function getUser(): ?User 
    {
        return $this->user;
    }

    private function getNonAccessiblePropertyValueOf(object $object, string $popname): mixed 
    {
        $reflection = new ReflectionObject($object);
        $prop = $reflection->getProperty($popname);
        $prop->setAccessible(true);
        $value = $prop->getValue($object);
        return $value;
    }
}

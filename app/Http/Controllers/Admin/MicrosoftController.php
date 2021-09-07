<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\mail;
use App\Models\User;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use Microsoft\Graph\Graph;

class MicrosoftController extends Controller
{
    public function ms_login(){
        $oauthClient = new GenericProvider([
            'clientId' => config('azure.appId'),
            'clientSecret' => config('azure.appSecret'),
            'redirectUri' => config('azure.redirectUri'),
            'urlAuthorize' => config('azure.authority') . config('azure.authorizeEndpoint'),
            'urlAccessToken' => config('azure.authority') . config('azure.tokenEndpoint'),
            'urlResourceOwnerDetails' => '',
            'scopes' => config('azure.scopes')
        ]);
        $authUrl = $oauthClient->getAuthorizationUrl();
        session(['oauthState' => $oauthClient->getState()]);
        return redirect()->away($authUrl);
    }
    public function callback(Request $request){
        $expectedState = session('oauthState');
        $request->session()->forget('oauthState');
        $providedState = $request->query('state');
        $authCode = $request->query('code');
        if (isset($authCode)) {
            $oauthClient = new GenericProvider([
                'clientId' => config('azure.appId'),
                'clientSecret' => config('azure.appSecret'),
                'redirectUri' => config('azure.redirectUri'),
                'urlAuthorize' => config('azure.authority') . config('azure.authorizeEndpoint'),
                'urlAccessToken' => config('azure.authority') . config('azure.tokenEndpoint'),
                'urlResourceOwnerDetails' => '',
                'scopes' => config('azure.scopes')
            ]);
            try {
                $accessToken = $oauthClient->getAccessToken('authorization_code', [
                    'code' => $authCode
                ]);
                $graph = new Graph();
                $graph->setAccessToken($accessToken->getToken());
                $user = $graph->createRequest('GET', '/me?$select=displayName,mail,mailboxSettings,userPrincipalName,id')
                    ->setReturnType(\Microsoft\Graph\Model\User::class)
                    ->execute();
                $check = explode('@', $user->getUserPrincipalName());
                if ($check[1] != 'newit.co.jp') {
                    return view('errors.403');
                }
                $data = User::query()->where('microsoft_id', $user->getId())->get()->first();
                if (!$data) {
                    $data = new User();
                    $data->name = $user->getDisplayName();
                    $data->microsoft_id = $user->getId();
                    $data->email = $user->getUserPrincipalName();
                    $mail = new mail();
                    $mail->mail_name = $user->getUserPrincipalName();
                    $mail->save();
                    $data->save();
                }
                $this->guard()->login($data);
                return redirect('/admin/');
            } catch (IdentityProviderException $e) {
                return redirect('/')
                    ->with('error', 'Error requesting access token')
                    ->with('errorDetail', json_encode($e->getResponseBody()));
            }
        }
        return redirect('/')
            ->with('error', $request->query('error'))
            ->with('errorDetail', $request->query('error_description'));
    }
    protected function guard()
    {
        return backpack_auth();
    }
}

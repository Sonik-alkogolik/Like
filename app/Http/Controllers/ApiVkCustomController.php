<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use App\Models\VkPageUser;
use App\Models\UserGroup;

class ApiVkCustomController extends Controller
{
    private $client;
    private $accessToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->accessToken = env('VKONTAKTE_ACCESS_TOKEN');
    }
 
    public function index()
    {
        return Socialite::driver('vkontakte')->scopes(['id'])->redirect();
    }

    public function callback()
    {
        $user_vk = Socialite::driver('vkontakte')->stateless()->user();
        Log::info('Does group exist?');
        $this->saveVkLink($user_vk);
        return redirect()->route('vk.vk-user');
    }

    // public function saveVkLink($user_vk)
    // {
    //     $userId = Auth::id();
    //     $existingUser = VkPageUser::where('user_id', $userId)->first();
        

    //     if ($existingUser) {
    //         return redirect()->route('vk.vk-user', ['user_vk' => $existingUser]);
    //     } else {
    //         $vkLink = "https://vk.com/id" . $user_vk->getId();
    //         VkPageUser::create([
    //             'user_id' => $userId,
    //             'vk_link' => $vkLink,
    //             'vk_user_id' => $user_vk->getId(),
    //         ]);
    //     }
    // }
    public function saveVkLink($user_vk)
    {
        $userId = Auth::id();
        $vkUserId = $user_vk->getId();
        $vkLink = "https://vk.com/id" . $vkUserId;
    
        // Проверяем, существует ли уже запись с таким vk_user_id
        $existingUser = VkPageUser::where('vk_user_id', $vkUserId)->first();
    
        if ($existingUser) {
            $message = "Страница привязана к другому аккаунту";
            return redirect()->route('vk.vk-user')->with('message', 'Страница привязана к другому аккаунту');
        } else {
            // Если записи нет, создаем новую
            VkPageUser::create([
                'user_id' => $userId,
                'vk_link' => $vkLink,
                'vk_user_id' => $vkUserId,
            ]);
    
            // Можете вернуть другое представление или перенаправить, если необходимо
            return redirect()->route('vk.vk-user', ['user_vk' => $existingUser]);
        }
    }

    // Удаление VK ссылки
    public function deleteVkLink()
    {
        $userId = Auth::id();

        $vkUser = VkPageUser::where('user_id', $userId)->first();

        if ($vkUser) {
            $vkUser->delete();
        }

        return redirect()->route('vk.vk-user')->with('status', 'Страница VK была успешно удалена.');
    }

    private function getUserGroups($userId)
    {
        $url = 'https://api.vk.com/method/groups.get';
        $params = [
            'access_token' => $this->accessToken,
            'user_id' => $userId,
            'v' => '5.199', 
            'extended' => 1,
        ];
    
        try {
            $response = $this->client->get($url, ['query' => $params]);
            $data = json_decode($response->getBody(), true);
    
            if (isset($data['response']['items'])) {
                return $data['response']['items']; // Список групп
            }
    
            return [];
        } catch (\Exception $e) {
            \Log::error('VK API request failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getUserById()
{
    $userId = auth()->id(); // Получаем текущего авторизованного пользователя

    if ($userId) {
        // Получаем данные о пользователе
        $users_vk_serfing = DB::table('vk_page_users')
            ->where('user_id', $userId)
            ->get();

        // Получаем группы пользователя
        $groups = $this->getUserGroups($userId);

        // Проверяем, есть ли ошибки
        if (empty($groups)) {
           //Log::error('VK API error: ' . 'No groups found or there was an issue with the request.');
        }

        // Возвращаем вид с данными
        return view('vk.vk-user', [
            'users_vk_serfing' => $users_vk_serfing,
            'groups' => $groups
        ]);
    } else {
        return redirect()->route('login');
    }
}

public function displayUserGroups()
    {
        // Проверка аутентификации пользователя
        if (Auth::check()) {
            // Извлечение данных из таблицы user_groups
            $userGroups = DB::table('user_groups')->get();

            // Передача данных в шаблон
            return view('vk.vk-groups', compact('userGroups'));
        } else {
            // Перенаправление на страницу входа, если пользователь не аутентифицирован
            return redirect()->route('login');
        }
    }

}

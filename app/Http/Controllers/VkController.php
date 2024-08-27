<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\UserGroup;


class VkController extends Controller
{

    protected $apiToken;

    public function __construct()
    {
        // Инициализируем токен API в конструкторе
        $this->apiToken = 'vk1.a.3WgPlNrH6DgZwgYKwFVJA63Is_2kkcAmSL_Ng9Oh00uFPblO-n6LbF3xxVBOoIoL9c0yuhycoET-Ofz2959vbduOxv3cdi87KyhlV-AAKb4-CrJHQYaox2uDkxR949SonlOaI1sKiLisEL58P0zmUr3GrmI4JI5je3p_00OP-5U0hr6ZxjhFXAaRfGGMK7P1y0QLjplxYonC0gqhlbOLng';
    }

    
    public function getGroups(Request $request)
    {
        $userId = $request->input('user_id');
        $accessToken = $request->input('access_token');
        $version = $request->input('v');
        $extended = $request->input('extended');

        $response = Http::get('https://api.vk.com/method/groups.get', [
            'user_id' => $userId,
            'access_token' => $accessToken,
            'v' => $version,
            'extended' => $extended,
        ]);

        return response()->json($response->json());
    }

    public function checkGroup(Request $request)
    {
        Log::info('Check Group Request Data:', $request->all());
    
        // Преобразуем ID группы к строке
        $groupId = (string) $request->input('group_id'); 
        $userId = $request->input('user_id');
        $accessToken = $request->input('access_token');
        $version = $request->input('v');
        $extended = $request->input('extended');
    
        Log::info("Fetching groups for user ID: $userId with group ID: $groupId");
    
        $response = Http::get('https://api.vk.com/method/groups.get', [
            'user_id' => $userId,
            'access_token' => $accessToken,
            'v' => $version,
            'extended' => $extended,
        ]);
    
        Log::info('VK API Response:', $response->json());
    
        if ($response->successful()) {
            $groups = collect($response->json()['response']['items'])->map(function($group) {
                Log::info('Group ID from VK:', ['id' => (string)$group['id']]); // Преобразуем ID к строке для сравнения
                return (string)$group['id'];
            });
    
            // Проверяем наличие groupId в списке групп
            $groupExists = $groups->contains($groupId);
    
            Log::info('Does group exist?', ['groupExists' => $groupExists, 'groupId' => $groupId, 'allGroupIds' => $groups]);
    
            return response()->json([
                'exists' => $groupExists
            ]);
        } else {
            Log::error('VK API Request failed:', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
    
            return response()->json([
                'error' => 'Failed to retrieve groups from VK API'
            ], 500);
        }
    }
    

    public function saveUserGroup(Request $request)
    {
        // Статичные данные
        // $userIdVk = '222222222';
        // $groupId = '33333333';
        // $groupLink = 'https://vk.com/science_technology';

        $userIdVk = $request->input('user_id'); 
        $groupLink = $request->input('group_link');
        Log::info('User ID VK:', ['user_id' => $userIdVk]);
        Log::info('Group Link:', ['group_link' => $groupLink]);

       $groupShortName = ltrim(parse_url($groupLink, PHP_URL_PATH), '/');
       $response = Http::get('https://api.vk.com/method/groups.getById', [
           'group_ids' => $groupShortName,
           'access_token' => $this->apiToken,
           'v' => '5.199',
       ]);

       $data = $response->json();
       $groups = $data['response']['groups'] ?? [];
       $groupId = !empty($groups) ? $groups[0]['id'] : null;
       Log::info('Extracted Group ID:', ['group_id' => $groupId]);
    
        // Проверка, существует ли запись с такими данными
        $exists = DB::table('user_groups')
            ->where('user_id_vk', $userIdVk)
            ->where('group_id', $groupId)
            ->exists();
    
        if (!$exists) {
           


            // Запись данных, если записи нет
            DB::table('user_groups')->insert([
                'user_id_vk' => $userIdVk,
                'group_id' => $groupId,
                'group_link' => $groupLink,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            return response()->json(['success' => 'UserGroup saved successfully.']);
        }
    
        // Если запись уже существует, ничего не возвращаем
        return response()->json(['message' => 'UserGroup already exists.']);
    }
 
    // public function saveUserGroup(Request $request)
    // {
    //     Log::info('Request data:', $request->all());
    
    //     $userIdVk = $request->input('user_id'); 
    //     $groupLink = $request->input('group_link');
    
    //     Log::info('User ID VK:', ['user_id' => $userIdVk]);
    //     Log::info('Group Link:', ['group_link' => $groupLink]);
    
    //     // Здесь можно добавить логику для проверки существования пользователя по user_id_vk
    
    //     // Запрос к VK API
    //     $groupShortName = ltrim(parse_url($groupLink, PHP_URL_PATH), '/');
    
    //     $response = Http::get('https://api.vk.com/method/groups.getById', [
    //         'group_ids' => $groupShortName,
    //         'access_token' => $this->apiToken,
    //         'v' => '5.199',
    //     ]);
    
    //     $data = $response->json();
    //     Log::info('VK API Full Response:', $data);
    
    //     if (isset($data['error'])) {
    //         Log::error('VK API error:', ['error' => $data['error']]);
    //         return response()->json(['error' => 'VK API error: ' . $data['error']['error_msg']], 400);
    //     }
    
    //     // Извлечение group_id
    //     $groups = $data['response']['groups'] ?? [];
    //     $groupId = !empty($groups) ? $groups[0]['id'] : null;
    //     Log::info('Extracted Group ID:', ['group_id' => $groupId]);
    
    //     // Сохранение данных
    //     if ($groupId && $userIdVk && $groupLink) {
    //         UserGroup::create([
    //             'group_id' => $groupId,
    //             'user_id_vk' => $userIdVk, // Обновлено на user_id_vk
    //             'group_link' => $groupLink,
    //         ]);
    //         Log::info('UserGroup saved successfully.');
    //     } else {
    //         Log::warning('Failed to save UserGroup due to missing data.');
    //     }
    // }

// public function saveGroup(Request $request)
// {
//     $groupLink = $request->input('group_link');
//     $accessToken = $request->input('access_token');
//     $version = $request->input('v');
//     $userId = $request->input('user_id');

//     // Логируем полученные параметры
//     Log::info('Received request data:', [
//         'group_link' => $groupLink,
//         'access_token' => $accessToken,
//         'version' => $version,
//         'user_id' => $userId,
//     ]);

//     if (!$groupLink || !$accessToken || !$version || !$userId) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Отсутствуют необходимые параметры.',
//             'data' => [
//                 'group_link' => $groupLink,
//                 'access_token' => $accessToken,
//                 'version' => $version,
//                 'user_id' => $userId,
//             ],
//         ]);
//     }

//     // Извлекаем имя группы из ссылки
//     $groupName = parse_url($groupLink, PHP_URL_PATH);
//     $groupName = trim($groupName, '/');

//     if (!$groupName) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Не удалось извлечь имя группы из ссылки.',
//             'data' => [
//                 'group_link' => $groupLink,
//                 'access_token' => $accessToken,
//                 'version' => $version,
//                 'user_id' => $userId,
//                 'group_name' => $groupName,
//             ],
//         ]);
//     }

//     Log::info('Formatted group name:', ['group_name' => $groupName]);

//     $response = Http::get('https://api.vk.com/method/groups.getById', [
//         'group_ids' => $groupName,
//         'access_token' => $accessToken,
//         'v' => $version,
//     ]);

//     // Логируем полный ответ
//     Log::info('VK API response:', $response->json());

//     if ($response->successful()) {
//         $data = $response->json();
//         $groupId = $data['response'][0]['id'] ?? null;
//         $groupName = $data['response'][0]['name'] ?? null;

//         // Логируем извлечённые данные
//         Log::info('Extracted group data:', [
//             'group_id' => $groupId,
//             'group_name' => $groupName,
//             'group_link' => $groupLink,
//         ]);

//         // Сохраняем ID группы, имя и ссылку в базу данных
//         DB::table('user_groups')->updateOrInsert(
//             ['user_id' => $userId, 'group_id' => $groupId],
//             ['user_id' => $userId, 'group_id' => $groupId, 'group_name' => $groupName, 'group_link' => $groupLink]
//         );

//         return response()->json([
//             'success' => true,
//             'group_id' => $groupId,
//             'group_name' => $groupName,
//             'group_link' => $groupLink,
//             'data' => $data,
//         ]);
//     } else {
//         return response()->json([
//             'success' => false,
//             'message' => 'Ошибка запроса к API VK.',
//             'data' => $response->json(),
//         ]);
//     }
// }



}

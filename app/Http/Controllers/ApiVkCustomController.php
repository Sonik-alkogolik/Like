<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\VkPageUser;

class ApiVkCustomController extends Controller
{

    public function getVkLink() {
        // Получаем запись из базы данных
        $vkPageUser = VkPageUser::first();
        if ($vkPageUser) {
            // Извлекаем ссылку из записи
            $vkLink = $vkPageUser->vk_link;
            // Выводим полученную ссылку для отладки
            //dd($vkLink);
            return $vkLink;
        } else {
            return null; // Если запись не найдена, вернем null
        }
    }

    public function saveVkLink(Request $request)
    {
        // Получите данные из запроса
        $userId = auth()->id(); // Получение ID текущего авторизованного пользователя
        $vkLink = $request->input('vk_link');

        // Извлекаем имя пользователя из ссылки
        $username = substr($vkLink, strrpos($vkLink, '/') + 1);

        // Сохраните данные в базе данных
        VkPageUser::create([
            'user_id' => $userId,
            'vk_link' => $vkLink,
            'vk_user_id' => $username,
        ]);

        // Перенаправьте пользователя обратно на страницу с информацией
        return redirect()->route('vk.vk-user');
    }

    public function getUserById(Request $request)
    {
        $vkLink = $this->getVkLink(); // Получение ссылки из базы данных

        // Инициализируем переменную $userData
        $userData = null;

        if ($vkLink) {
            // Получаем vk_user_id из ссылки
            $vkUserId = substr($vkLink, strrpos($vkLink, '/') + 1);

            // Ваш сервисный ключ доступа
            $serviceAccessToken = '16bfae7316bfae7316bfae732915a78708116bf16bfae737090328b453d90552870af0e';

            $response = Http::get('https://api.vk.com/method/users.get', [
                'user_ids' => $vkUserId, // Используем vk_user_id для запроса
                'access_token' => $serviceAccessToken,
                'v' => '5.199',
                'fields' => 'screen_name,first_name,last_name,is_closed',
            ]);

            // Проверяем успешность запроса
            if ($response->successful() && isset($response['response'][0])) {
                $userData = $response['response'][0];
            }
        }

        // Возвращаем представление с передачей переменной $userData
        return view('vk.vk-user', ['userData' => $userData]);
    }

    public function deleteVkLink(Request $request)
    {
        // Получение ID текущего авторизованного пользователя
        $userId = auth()->id();

        // Удаление записи из базы данных
        VkPageUser::where('user_id', $userId)->delete();

        // Перенаправление пользователя обратно на страницу с информацией
        return redirect()->route('vk.vk-user');
    }



}

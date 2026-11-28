<?php

use App\Models\Category;
use App\Models\Country;
use App\Models\Film;
use App\Models\Gender;
use App\Models\Rating;
use App\Models\Review;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->gender = Gender::create(['name' => 'Мужской']);
    $this->country = Country::create(['name' => 'Казахстан']);
    $this->category = Category::create(['name' => 'Фантастика']);

    $this->film = Film::create([
        'name' => 'Интерстеллар',
        'country_id' => $this->country->id,
        'duration' => 169,
        'year_of_issue' => 2014,
        'age' => 12,
        'link_video' => 'https://example.com',
    ]);
    $this->film->categories()->attach($this->category->id);
});

// --- ЭТАП 1: Открытые эндпоинты ---

test('получение списка фильмов с фильтрацией и пагинацией', function () {
    $this->getJson('/api/v1/films?page=1&size=10&search=Интер')
        ->assertStatus(200)
        ->assertJsonStructure([
            'page',
            'size',
            'total',
            'films' => [
                '*' => ['id', 'name', 'duration', 'year_of_issue', 'age', 'country', 'categories', 'ratingAvg', 'reviewCount'],
            ],
        ])
        ->assertJsonPath('total', 1);
});

test('получение фильма по id и 404 при неверном id', function () {
    $this->getJson("/api/v1/films/{$this->film->id}")
        ->assertStatus(200)
        ->assertJsonPath('name', 'Интерстеллар');

    $this->getJson('/api/v1/films/99999')
        ->assertStatus(404)
        ->assertJson(['message' => 'Film not found']);
});

test('получение справочников (categories, countries, genders)', function () {
    $this->getJson('/api/v1/categories')->assertStatus(200)->assertJsonStructure(['categories']);
    $this->getJson('/api/v1/countries')->assertStatus(200)->assertJsonStructure(['countries']);
    $this->getJson('/api/v1/genders')->assertStatus(200)->assertJsonStructure(['genders']);
});

test('регистрация, вход и выход пользователя', function () {
    // Регистрация
    $signup = $this->postJson('/api/v1/auth/signup', [
        'fio' => 'Иванов Иван',
        'email' => 'ivanov@test.kz',
        'password' => 'secret123',
        'birthday' => '2000-05-10',
        'gender_id' => $this->gender->id,
    ]);

    $signup->assertStatus(201)
        ->assertJsonStructure(['status', 'token', 'id', 'fio']);

    // Неверный пароль при входе
    $this->postJson('/api/v1/auth/signin', [
        'email' => 'ivanov@test.kz',
        'password' => 'wrongpass',
    ])->assertStatus(401)
        ->assertJson(['status' => 'invalid', 'message' => 'Wrong email or password']);

    // Успешный вход
    $signin = $this->postJson('/api/v1/auth/signin', [
        'email' => 'ivanov@test.kz',
        'password' => 'secret123',
    ])->assertStatus(200);

    $token = $signin->json('token');

    // Выход
    $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/v1/auth/signout')
        ->assertStatus(200)
        ->assertJson(['status' => 'success']);
});

// --- ЭТАП 2: Закрытые эндпоинты ---

test('закрытые эндпоинты недоступны без токена', function () {
    $this->getJson('/api/v1/users/1')->assertStatus(401);
    $this->putJson('/api/v1/users')->assertStatus(401);
    $this->deleteJson('/api/v1/users')->assertStatus(401);
});

test('просмотр, обновление и удаление профиля пользователя', function () {
    $user = User::create([
        'fio' => 'Петров Пётр',
        'email' => 'petrov@test.kz',
        'password' => 'secret123',
        'birthday' => '1999-01-01',
        'gender_id' => $this->gender->id,
    ]);

    Sanctum::actingAs($user);

    // Просмотр профиля
    $this->getJson("/api/v1/users/{$user->id}")
        ->assertStatus(200)
        ->assertJson([
            'id' => $user->id,
            'fio' => 'Петров Пётр',
            'email' => 'petrov@test.kz',
            'birthday' => '1999-01-01',
            'reviewCount' => 0,
            'ratingCount' => 0,
        ]);

    // Обновление профиля
    $this->putJson('/api/v1/users', [
        'fio' => 'Петров Алексей',
        'email' => 'petrov@test.kz',
        'birthday' => '1999-02-02',
        'gender_id' => $this->gender->id,
    ])->assertStatus(200)
        ->assertJson(['status' => 'success']);

    expect($user->fresh()->fio)->toBe('Петров Алексей');

    // Удаление аккаунта
    $this->deleteJson('/api/v1/users')->assertStatus(204);
    expect(User::find($user->id))->toBeNull();
    expect(User::withTrashed()->find($user->id))->not->toBeNull();
});

test('добавление, просмотр и удаление отзывов пользователя', function () {
    $user = User::create([
        'fio' => 'Сидоров Сидор',
        'email' => 'sidorov@test.kz',
        'password' => 'secret123',
        'birthday' => '1998-03-03',
        'gender_id' => $this->gender->id,
    ]);

    Sanctum::actingAs($user);

    // Добавление отзыва
    $create = $this->postJson("/api/v1/users/{$user->id}/reviews", [
        'film_id' => $this->film->id,
        'message' => 'Отличный фильм!',
    ]);

    $create->assertStatus(201)
        ->assertJsonPath('message', 'Отличный фильм!')
        ->assertJsonPath('is_approved', 0);

    $reviewId = $create->json('id');

    // Список отзывов пользователя
    $this->getJson("/api/v1/users/{$user->id}/reviews")
        ->assertStatus(200)
        ->assertJsonCount(1, 'reviews');

    // В публичном списке отзыв не виден до одобрения админом
    $this->getJson("/api/v1/films/{$this->film->id}/reviews")
        ->assertStatus(200)
        ->assertJsonCount(0, 'reviews');

    // Одобряем отзыв и проверяем публичный список
    Review::find($reviewId)->update(['is_approved' => 1]);
    $this->getJson("/api/v1/films/{$this->film->id}/reviews")
        ->assertStatus(200)
        ->assertJsonCount(1, 'reviews');

    // Удаление отзыва
    $this->deleteJson("/api/v1/users/{$user->id}/reviews/{$reviewId}")
        ->assertStatus(204);
});

test('добавление, защита от дубликата, просмотр и удаление оценок', function () {
    $user = User::create([
        'fio' => 'Тестов Тест',
        'email' => 'testov@test.kz',
        'password' => 'secret123',
        'birthday' => '2001-07-07',
        'gender_id' => $this->gender->id,
    ]);

    Sanctum::actingAs($user);

    // Выставление оценки
    $create = $this->postJson("/api/v1/users/{$user->id}/ratings", [
        'film_id' => $this->film->id,
        'ball' => 5,
    ]);

    $create->assertStatus(201)
        ->assertJsonPath('score', 5);

    $ratingId = $create->json('id');

    // Повторная оценка тому же фильму -> 401 Score exist
    $this->postJson("/api/v1/users/{$user->id}/ratings", [
        'film_id' => $this->film->id,
        'ball' => 4,
    ])->assertStatus(401)
        ->assertJson([
            'status' => 'invalid',
            'message' => 'Score exist',
        ]);

    // Список оценок
    $this->getJson("/api/v1/users/{$user->id}/ratings")
        ->assertStatus(200)
        ->assertJsonCount(1, 'ratings')
        ->assertJsonPath('ratings.0.score', 5);

    // Удаление оценки
    $this->deleteJson("/api/v1/users/{$user->id}/ratings/{$ratingId}")
        ->assertStatus(204);

    expect(Rating::find($ratingId))->toBeNull();
});

// --- ПРОВЕРКА ГРАНИЧНЫХ СЛУЧАЕВ И СПЕЦИФИКАЦИИ ---

test('сортировка фильмов по name, year и rating (asc и desc) и фильтры по стране/жанру', function () {
    $country2 = Country::create(['name' => 'США']);
    $category2 = Category::create(['name' => 'Драма']);

    $film2 = Film::create([
        'name' => 'Аватар',
        'country_id' => $country2->id,
        'duration' => 162,
        'year_of_issue' => 2009,
        'age' => 12,
        'link_video' => 'https://example.com',
    ]);
    $film2->categories()->attach($category2->id);

    $user = User::create([
        'fio' => 'Оценщик',
        'email' => 'rater@test.kz',
        'password' => 'secret123',
        'birthday' => '2000-01-01',
        'gender_id' => $this->gender->id,
    ]);

    // Интерстеллар: рейтинг 5, Аватар: рейтинг 3
    Rating::create(['film_id' => $this->film->id, 'user_id' => $user->id, 'ball' => 5]);
    Rating::create(['film_id' => $film2->id, 'user_id' => $user->id, 'ball' => 3]);

    // Сортировка по имени asc -> Аватар первый
    $this->getJson('/api/v1/films?sortBy=name&sortDir=asc')
        ->assertStatus(200)
        ->assertJsonPath('films.0.name', 'Аватар');

    // Сортировка по году desc -> Интерстеллар (2014) первый
    $this->getJson('/api/v1/films?sortBy=year&sortDir=desc')
        ->assertStatus(200)
        ->assertJsonPath('films.0.name', 'Интерстеллар');

    // Сортировка по рейтингу desc -> Интерстеллар (5) первый
    $this->getJson('/api/v1/films?sortBy=rating&sortDir=desc')
        ->assertStatus(200)
        ->assertJsonPath('films.0.name', 'Интерстеллар')
        ->assertJsonPath('films.0.ratingAvg', 5);

    // Фильтр по стране и жанру
    $this->getJson("/api/v1/films?country={$country2->id}&category={$category2->id}")
        ->assertStatus(200)
        ->assertJsonPath('total', 1)
        ->assertJsonPath('films.0.name', 'Аватар');
});

test('404 при запросе отзывов к несуществующему фильму и подсчет filmCount в справочниках', function () {
    $this->getJson('/api/v1/films/99999/reviews')
        ->assertStatus(404)
        ->assertJson(['message' => 'Film not found']);

    $this->getJson('/api/v1/categories')
        ->assertStatus(200)
        ->assertJsonPath('categories.0.filmCount', 1);

    $this->getJson('/api/v1/countries')
        ->assertStatus(200)
        ->assertJsonPath('countries.0.filmCount', 1);
});

test('валидация регистрации и обновления профиля (дубликат email, короткий пароль)', function () {
    $user1 = User::create([
        'fio' => 'Первый',
        'email' => 'first@test.kz',
        'password' => 'secret123',
        'birthday' => '2000-01-01',
        'gender_id' => $this->gender->id,
    ]);

    $user2 = User::create([
        'fio' => 'Второй',
        'email' => 'second@test.kz',
        'password' => 'secret123',
        'birthday' => '2000-01-01',
        'gender_id' => $this->gender->id,
    ]);

    // Регистрация с занятым email -> 422
    $this->postJson('/api/v1/auth/signup', [
        'fio' => 'Дубль',
        'email' => 'first@test.kz',
        'password' => 'secret123',
        'birthday' => '2000-01-01',
        'gender_id' => $this->gender->id,
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['email']);

    // Обновление профиля на чужой email -> 422
    Sanctum::actingAs($user2);
    $this->putJson('/api/v1/users', [
        'fio' => 'Второй',
        'email' => 'first@test.kz',
        'birthday' => '2000-01-01',
        'gender_id' => $this->gender->id,
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('коды 404 и защита от доступа к чужим отзывам и оценкам', function () {
    $owner = User::create([
        'fio' => 'Владелец',
        'email' => 'owner@test.kz',
        'password' => 'secret123',
        'birthday' => '2000-01-01',
        'gender_id' => $this->gender->id,
    ]);

    $stranger = User::create([
        'fio' => 'Чужой',
        'email' => 'stranger@test.kz',
        'password' => 'secret123',
        'birthday' => '2000-01-01',
        'gender_id' => $this->gender->id,
    ]);

    $review = Review::create([
        'film_id' => $this->film->id,
        'user_id' => $owner->id,
        'message' => 'Мой отзыв',
        'is_approved' => 0,
    ]);

    $rating = Rating::create([
        'film_id' => $this->film->id,
        'user_id' => $owner->id,
        'ball' => 4,
    ]);

    Sanctum::actingAs($stranger);

    // 404 при несуществующем user-id
    $this->getJson('/api/v1/users/99999')->assertStatus(404)->assertJson(['message' => 'User not found']);
    $this->getJson('/api/v1/users/99999/reviews')->assertStatus(404)->assertJson(['message' => 'User not found']);
    $this->getJson('/api/v1/users/99999/ratings')->assertStatus(404)->assertJson(['message' => 'User not found']);
    $this->deleteJson('/api/v1/users/99999/reviews/1')->assertStatus(404)->assertJson(['message' => 'User not found']);
    $this->deleteJson('/api/v1/users/99999/ratings/1')->assertStatus(404)->assertJson(['message' => 'User not found']);

    // Запрет удаления чужого отзыва/оценки (403)
    $this->deleteJson("/api/v1/users/{$owner->id}/reviews/{$review->id}")->assertStatus(403);
    $this->deleteJson("/api/v1/users/{$owner->id}/ratings/{$rating->id}")->assertStatus(403);

    // Входим под владельцем и проверяем 404 на несуществующий отзыв/оценку
    Sanctum::actingAs($owner);
    $this->deleteJson("/api/v1/users/{$owner->id}/reviews/99999")
        ->assertStatus(404)
        ->assertJson(['message' => 'Review not found']);

    $this->deleteJson("/api/v1/users/{$owner->id}/ratings/99999")
        ->assertStatus(404)
        ->assertJson(['message' => 'Rating not found']);
});

test('валидация диапазона оценки ball (только от 1 до 5)', function () {
    $user = User::create([
        'fio' => 'Валидатор',
        'email' => 'valid@test.kz',
        'password' => 'secret123',
        'birthday' => '2000-01-01',
        'gender_id' => $this->gender->id,
    ]);

    Sanctum::actingAs($user);

    $this->postJson("/api/v1/users/{$user->id}/ratings", [
        'film_id' => $this->film->id,
        'ball' => 0,
    ])->assertStatus(422)->assertJsonValidationErrors(['ball']);

    $this->postJson("/api/v1/users/{$user->id}/ratings", [
        'film_id' => $this->film->id,
        'ball' => 6,
    ])->assertStatus(422)->assertJsonValidationErrors(['ball']);
});

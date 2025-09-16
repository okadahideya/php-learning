<?php

namespace PhpLearning\Testing;

use InvalidArgumentException;

/**
 * ユーザーリポジトリクラス - PHPUnitテスト学習用サンプル
 * ユーザーデータの永続化を管理するクラス
 */
class UserRepository
{
    private array $users = [];
    private DatabaseInterface $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    /**
     * ユーザーを保存
     */
    public function save(User $user): void
    {
        $data = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'birth_date' => $user->getBirthDate()->format('Y-m-d'),
            'roles' => implode(',', $user->getRoles()),
            'active' => $user->isActive() ? 1 : 0,
        ];

        $this->database->insert('users', $data);
        $this->users[$user->getEmail()] = $user;
    }

    /**
     * メールアドレスでユーザーを検索
     */
    public function findByEmail(string $email): ?User
    {
        // まずメモリから検索
        if (isset($this->users[$email])) {
            return $this->users[$email];
        }

        // データベースから検索
        $userData = $this->database->find('users', ['email' => $email]);
        
        if (!$userData) {
            return null;
        }

        $user = new User(
            $userData['name'],
            $userData['email'],
            new \DateTime($userData['birth_date'])
        );

        if (!empty($userData['roles'])) {
            $roles = explode(',', $userData['roles']);
            foreach ($roles as $role) {
                $user->addRole(trim($role));
            }
        }

        $user->setActive((bool) $userData['active']);
        
        // メモリにキャッシュ
        $this->users[$email] = $user;

        return $user;
    }

    /**
     * 全ユーザーを取得
     */
    public function findAll(): array
    {
        $allData = $this->database->findAll('users');
        $users = [];

        foreach ($allData as $userData) {
            $user = new User(
                $userData['name'],
                $userData['email'],
                new \DateTime($userData['birth_date'])
            );

            if (!empty($userData['roles'])) {
                $roles = explode(',', $userData['roles']);
                foreach ($roles as $role) {
                    $user->addRole(trim($role));
                }
            }

            $user->setActive((bool) $userData['active']);
            $users[] = $user;
            
            // メモリにキャッシュ
            $this->users[$userData['email']] = $user;
        }

        return $users;
    }

    /**
     * 管理者ユーザーを取得
     */
    public function findAdmins(): array
    {
        $allUsers = $this->findAll();
        
        return array_filter($allUsers, function (User $user) {
            return $user->isAdmin();
        });
    }

    /**
     * アクティブなユーザーを取得
     */
    public function findActive(): array
    {
        $allUsers = $this->findAll();
        
        return array_filter($allUsers, function (User $user) {
            return $user->isActive();
        });
    }

    /**
     * ユーザーを更新
     */
    public function update(User $user): void
    {
        $data = [
            'name' => $user->getName(),
            'birth_date' => $user->getBirthDate()->format('Y-m-d'),
            'roles' => implode(',', $user->getRoles()),
            'active' => $user->isActive() ? 1 : 0,
        ];

        $this->database->update('users', ['email' => $user->getEmail()], $data);
        $this->users[$user->getEmail()] = $user;
    }

    /**
     * ユーザーを削除
     */
    public function delete(string $email): void
    {
        $this->database->delete('users', ['email' => $email]);
        unset($this->users[$email]);
    }

    /**
     * メールアドレスの重複チェック
     */
    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    /**
     * ユーザー数を取得
     */
    public function count(): int
    {
        return $this->database->count('users');
    }

    /**
     * 年齢範囲でユーザーを検索
     */
    public function findByAgeRange(int $minAge, int $maxAge): array
    {
        if ($minAge > $maxAge) {
            throw new InvalidArgumentException('Minimum age cannot be greater than maximum age');
        }

        $allUsers = $this->findAll();
        
        return array_filter($allUsers, function (User $user) use ($minAge, $maxAge) {
            $age = $user->getAge();
            return $age >= $minAge && $age <= $maxAge;
        });
    }

    /**
     * キャッシュをクリア
     */
    public function clearCache(): void
    {
        $this->users = [];
    }
}
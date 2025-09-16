<?php

namespace PhpLearning\Testing;

use DateTime;
use InvalidArgumentException;

/**
 * ユーザークラス - PHPUnitテスト学習用サンプル
 * ユーザー情報を管理するクラス
 */
class User
{
    private string $name;
    private string $email;
    private DateTime $birthDate;
    private array $roles = [];
    private bool $active = true;

    public function __construct(string $name, string $email, DateTime $birthDate)
    {
        $this->setName($name);
        $this->setEmail($email);
        $this->setBirthDate($birthDate);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $trimmedName = trim($name);
        
        if (empty($trimmedName)) {
            throw new InvalidArgumentException('Name cannot be empty');
        }

        if (strlen($trimmedName) > 100) {
            throw new InvalidArgumentException('Name cannot exceed 100 characters');
        }

        $this->name = $trimmedName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $trimmedEmail = trim($email);
        
        if (!filter_var($trimmedEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }

        $this->email = $trimmedEmail;
    }

    public function getBirthDate(): DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(DateTime $birthDate): void
    {
        $now = new DateTime();
        
        if ($birthDate >= $now) {
            throw new InvalidArgumentException('Birth date must be in the past');
        }

        $this->birthDate = $birthDate;
    }

    /**
     * 年齢を計算
     */
    public function getAge(): int
    {
        $now = new DateTime();
        $interval = $this->birthDate->diff($now);
        
        return $interval->y;
    }

    /**
     * 成人かどうか判定
     */
    public function isAdult(): bool
    {
        return $this->getAge() >= 18;
    }

    /**
     * 役割を追加
     */
    public function addRole(string $role): void
    {
        $role = trim($role);
        
        if (empty($role)) {
            throw new InvalidArgumentException('Role cannot be empty');
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    /**
     * 役割を削除
     */
    public function removeRole(string $role): void
    {
        $key = array_search($role, $this->roles, true);
        
        if ($key !== false) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles); // インデックス再構築
        }
    }

    /**
     * 指定した役割を持っているか
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    /**
     * 全ての役割を取得
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * 管理者かどうか
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * アクティブかどうか
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * アクティブ状態を設定
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * アカウントを無効化
     */
    public function deactivate(): void
    {
        $this->active = false;
    }

    /**
     * アカウントを有効化
     */
    public function activate(): void
    {
        $this->active = true;
    }

    /**
     * フルネーム（表示用）を取得
     */
    public function getDisplayName(): string
    {
        return $this->name;
    }

    /**
     * ユーザー情報を配列で取得
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'birth_date' => $this->birthDate->format('Y-m-d'),
            'age' => $this->getAge(),
            'roles' => $this->roles,
            'active' => $this->active,
            'is_adult' => $this->isAdult(),
            'is_admin' => $this->isAdmin(),
        ];
    }

    /**
     * ユーザー情報を JSON で取得
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 他のユーザーと同じかどうか（メールアドレスで判定）
     */
    public function equals(User $other): bool
    {
        return $this->email === $other->getEmail();
    }
}
<?php

namespace PhpLearning\Testing\Tests\Unit;

use PhpLearning\Testing\User;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use DateTime;

/**
 * Userクラスのユニットテスト
 * オブジェクトのプロパティとメソッドのテスト例を学習できます
 */
class UserTest extends TestCase
{
    private User $user;
    private DateTime $birthDate;

    protected function setUp(): void
    {
        $this->birthDate = new DateTime('1990-01-01');
        $this->user = new User('田中太郎', 'tanaka@example.com', $this->birthDate);
    }

    // ===== コンストラクタのテスト =====

    /**
     * @test
     */
    public function 正常なデータでユーザーが作成できること(): void
    {
        $user = new User('佐藤花子', 'sato@example.com', new DateTime('1985-06-15'));
        
        $this->assertEquals('佐藤花子', $user->getName());
        $this->assertEquals('sato@example.com', $user->getEmail());
        $this->assertEquals('1985-06-15', $user->getBirthDate()->format('Y-m-d'));
    }

    // ===== 名前のテスト =====

    /**
     * @test
     */
    public function 名前が正しく設定・取得できること(): void
    {
        $this->assertEquals('田中太郎', $this->user->getName());
    }

    /**
     * @test
     */
    public function 名前を変更できること(): void
    {
        $this->user->setName('山田花子');
        $this->assertEquals('山田花子', $this->user->getName());
    }

    /**
     * @test
     */
    public function 空の名前でExceptionが発生すること(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Name cannot be empty');
        
        $this->user->setName('');
    }

    /**
     * @test
     */
    public function 空白のみの名前でExceptionが発生すること(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Name cannot be empty');
        
        $this->user->setName('   ');
    }

    /**
     * @test
     */
    public function 長すぎる名前でExceptionが発生すること(): void
    {
        $longName = str_repeat('あ', 101); // 101文字
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Name cannot exceed 100 characters');
        
        $this->user->setName($longName);
    }

    /**
     * @test
     */
    public function 名前の前後の空白が除去されること(): void
    {
        $this->user->setName('  田中太郎  ');
        $this->assertEquals('田中太郎', $this->user->getName());
    }

    // ===== メールアドレスのテスト =====

    /**
     * @test
     */
    public function 正しいメールアドレスが設定できること(): void
    {
        $this->user->setEmail('new@example.com');
        $this->assertEquals('new@example.com', $this->user->getEmail());
    }

    /**
     * @test
     * @dataProvider invalidEmailProvider
     */
    public function 不正なメールアドレスでExceptionが発生すること($email): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        $this->user->setEmail($email);
    }

    /**
     * 不正なメールアドレスのデータプロバイダー
     */
    public static function invalidEmailProvider(): array
    {
        return [
            '空文字' => [''],
            'アットマーク無し' => ['testexample.com'],
            'ドメイン無し' => ['test@'],
            'ローカル部無し' => ['@example.com'],
            '空白を含む' => ['test @example.com'],
            '不正な文字' => ['test@exam ple.com'],
        ];
    }

    // ===== 生年月日と年齢のテスト =====

    /**
     * @test
     */
    public function 年齢が正しく計算されること(): void
    {
        $now = new DateTime();
        $currentYear = (int) $now->format('Y');
        $expectedAge = $currentYear - 1990;
        
        $this->assertEquals($expectedAge, $this->user->getAge());
    }

    /**
     * @test
     */
    public function 未来の生年月日でExceptionが発生すること(): void
    {
        $futureDate = new DateTime('+1 year');
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Birth date must be in the past');
        
        $this->user->setBirthDate($futureDate);
    }

    /**
     * @test
     */
    public function 成人判定が正しく動作すること(): void
    {
        // 20歳のユーザー
        $adultUser = new User('成人', 'adult@example.com', new DateTime('-20 years'));
        $this->assertTrue($adultUser->isAdult());
        
        // 17歳のユーザー
        $minorUser = new User('未成年', 'minor@example.com', new DateTime('-17 years'));
        $this->assertFalse($minorUser->isAdult());
    }

    // ===== 役割管理のテスト =====

    /**
     * @test
     */
    public function 役割を追加できること(): void
    {
        $this->user->addRole('admin');
        $this->assertTrue($this->user->hasRole('admin'));
    }

    /**
     * @test
     */
    public function 複数の役割を追加できること(): void
    {
        $this->user->addRole('admin');
        $this->user->addRole('editor');
        $this->user->addRole('viewer');
        
        $this->assertTrue($this->user->hasRole('admin'));
        $this->assertTrue($this->user->hasRole('editor'));
        $this->assertTrue($this->user->hasRole('viewer'));
        
        $roles = $this->user->getRoles();
        $this->assertCount(3, $roles);
        $this->assertContains('admin', $roles);
        $this->assertContains('editor', $roles);
        $this->assertContains('viewer', $roles);
    }

    /**
     * @test
     */
    public function 重複する役割は追加されないこと(): void
    {
        $this->user->addRole('admin');
        $this->user->addRole('admin'); // 重複
        
        $roles = $this->user->getRoles();
        $this->assertCount(1, $roles);
        $this->assertEquals(['admin'], $roles);
    }

    /**
     * @test
     */
    public function 役割を削除できること(): void
    {
        $this->user->addRole('admin');
        $this->user->addRole('editor');
        
        $this->user->removeRole('admin');
        
        $this->assertFalse($this->user->hasRole('admin'));
        $this->assertTrue($this->user->hasRole('editor'));
    }

    /**
     * @test
     */
    public function 存在しない役割の削除は無視されること(): void
    {
        $this->user->addRole('admin');
        
        $this->user->removeRole('nonexistent');
        
        $this->assertTrue($this->user->hasRole('admin'));
        $this->assertCount(1, $this->user->getRoles());
    }

    /**
     * @test
     */
    public function 空の役割名でExceptionが発生すること(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Role cannot be empty');
        
        $this->user->addRole('');
    }

    /**
     * @test
     */
    public function 管理者判定が正しく動作すること(): void
    {
        $this->assertFalse($this->user->isAdmin());
        
        $this->user->addRole('admin');
        $this->assertTrue($this->user->isAdmin());
        
        $this->user->removeRole('admin');
        $this->assertFalse($this->user->isAdmin());
    }

    // ===== アクティブ状態のテスト =====

    /**
     * @test
     */
    public function デフォルトでアクティブであること(): void
    {
        $this->assertTrue($this->user->isActive());
    }

    /**
     * @test
     */
    public function アクティブ状態を変更できること(): void
    {
        $this->user->setActive(false);
        $this->assertFalse($this->user->isActive());
        
        $this->user->setActive(true);
        $this->assertTrue($this->user->isActive());
    }

    /**
     * @test
     */
    public function 無効化と有効化ができること(): void
    {
        $this->user->deactivate();
        $this->assertFalse($this->user->isActive());
        
        $this->user->activate();
        $this->assertTrue($this->user->isActive());
    }

    // ===== データ変換のテスト =====

    /**
     * @test
     */
    public function 配列に変換できること(): void
    {
        $this->user->addRole('admin');
        $this->user->addRole('editor');
        
        $expected = [
            'name' => '田中太郎',
            'email' => 'tanaka@example.com',
            'birth_date' => '1990-01-01',
            'age' => $this->user->getAge(),
            'roles' => ['admin', 'editor'],
            'active' => true,
            'is_adult' => true,
            'is_admin' => true,
        ];
        
        $this->assertEquals($expected, $this->user->toArray());
    }

    /**
     * @test
     */
    public function JSONに変換できること(): void
    {
        $this->user->addRole('viewer');
        
        $json = $this->user->toJson();
        $decoded = json_decode($json, true);
        
        $this->assertEquals($this->user->getName(), $decoded['name']);
        $this->assertEquals($this->user->getEmail(), $decoded['email']);
        $this->assertEquals(['viewer'], $decoded['roles']);
    }

    // ===== ユーザー比較のテスト =====

    /**
     * @test
     */
    public function 同じメールアドレスのユーザーは等価と判定されること(): void
    {
        $user1 = new User('田中太郎', 'tanaka@example.com', new DateTime('1990-01-01'));
        $user2 = new User('田中花子', 'tanaka@example.com', new DateTime('1985-06-15')); // 名前・生年月日が違う
        
        $this->assertTrue($user1->equals($user2));
    }

    /**
     * @test
     */
    public function 異なるメールアドレスのユーザーは等価ではないと判定されること(): void
    {
        $user1 = new User('田中太郎', 'tanaka@example.com', new DateTime('1990-01-01'));
        $user2 = new User('田中太郎', 'yamada@example.com', new DateTime('1990-01-01')); // メールアドレスのみ違う
        
        $this->assertFalse($user1->equals($user2));
    }

    // ===== エッジケースのテスト =====

    /**
     * @test
     */
    public function 誕生日当日の年齢計算(): void
    {
        $today = new DateTime();
        $birthDate = new DateTime('-25 years'); // 25年前の今日
        
        $user = new User('誕生日', 'birthday@example.com', $birthDate);
        $this->assertEquals(25, $user->getAge());
    }

    /**
     * @test
     */
    public function うるう年の処理(): void
    {
        $leapYear = new DateTime('2000-02-29'); // うるう年
        $user = new User('うるう年', 'leap@example.com', $leapYear);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('2000-02-29', $user->getBirthDate()->format('Y-m-d'));
    }

    /**
     * @test
     */
    public function 極端に長い有効な名前(): void
    {
        $longName = str_repeat('あ', 100); // 100文字（制限ギリギリ）
        
        $this->user->setName($longName);
        $this->assertEquals($longName, $this->user->getName());
    }
}
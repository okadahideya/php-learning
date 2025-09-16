<?php

namespace PhpLearning\Testing\Tests\Feature;

use PhpLearning\Testing\User;
use PhpLearning\Testing\UserRepository;
use PhpLearning\Testing\DatabaseInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use DateTime;

/**
 * UserRepositoryクラスの機能テスト
 * モックオブジェクトを使用したテスト例を学習できます
 */
class UserRepositoryTest extends TestCase
{
    private UserRepository $repository;
    /** @var DatabaseInterface&MockObject */
    private DatabaseInterface $databaseMock;
    private User $sampleUser;

    protected function setUp(): void
    {
        // DatabaseInterfaceのモックオブジェクトを作成
        $this->databaseMock = $this->createMock(DatabaseInterface::class);
        $this->repository = new UserRepository($this->databaseMock);
        
        // テスト用のサンプルユーザー
        $this->sampleUser = new User(
            '田中太郎',
            'tanaka@example.com',
            new DateTime('1990-01-01')
        );
        $this->sampleUser->addRole('admin');
        $this->sampleUser->addRole('editor');
    }

    // ===== ユーザー保存のテスト =====

    /**
     * @test
     */
    public function ユーザーが正しく保存されること(): void
    {
        // モックの期待値を設定
        $expectedData = [
            'name' => '田中太郎',
            'email' => 'tanaka@example.com',
            'birth_date' => '1990-01-01',
            'roles' => 'admin,editor',
            'active' => 1,
        ];

        $this->databaseMock
            ->expects($this->once())
            ->method('insert')
            ->with('users', $expectedData)
            ->willReturn(true);

        // テスト実行
        $this->repository->save($this->sampleUser);
        
        // メモリキャッシュにも保存されていることを確認
        // (内部実装の詳細なので、この確認は通常は不要だが学習のため)
        $this->assertTrue(true); // saveメソッドが例外を投げずに完了したことを確認
    }

    // ===== メールによるユーザー検索のテスト =====

    /**
     * @test
     */
    public function メールアドレスでユーザーが見つかること(): void
    {
        $userData = [
            'name' => '田中太郎',
            'email' => 'tanaka@example.com',
            'birth_date' => '1990-01-01',
            'roles' => 'admin,editor',
            'active' => 1,
        ];

        $this->databaseMock
            ->expects($this->once())
            ->method('find')
            ->with('users', ['email' => 'tanaka@example.com'])
            ->willReturn($userData);

        $user = $this->repository->findByEmail('tanaka@example.com');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('田中太郎', $user->getName());
        $this->assertEquals('tanaka@example.com', $user->getEmail());
        $this->assertTrue($user->hasRole('admin'));
        $this->assertTrue($user->hasRole('editor'));
        $this->assertTrue($user->isActive());
    }

    /**
     * @test
     */
    public function 存在しないメールアドレスでnullが返されること(): void
    {
        $this->databaseMock
            ->expects($this->once())
            ->method('find')
            ->with('users', ['email' => 'notfound@example.com'])
            ->willReturn(null);

        $user = $this->repository->findByEmail('notfound@example.com');

        $this->assertNull($user);
    }

    /**
     * @test
     */
    public function 役割が空の場合の処理(): void
    {
        $userData = [
            'name' => '佐藤花子',
            'email' => 'sato@example.com',
            'birth_date' => '1985-06-15',
            'roles' => '',
            'active' => 1,
        ];

        $this->databaseMock
            ->expects($this->once())
            ->method('find')
            ->with('users', ['email' => 'sato@example.com'])
            ->willReturn($userData);

        $user = $this->repository->findByEmail('sato@example.com');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEmpty($user->getRoles());
    }

    // ===== 全ユーザー取得のテスト =====

    /**
     * @test
     */
    public function 全ユーザーが正しく取得されること(): void
    {
        $usersData = [
            [
                'name' => '田中太郎',
                'email' => 'tanaka@example.com',
                'birth_date' => '1990-01-01',
                'roles' => 'admin',
                'active' => 1,
            ],
            [
                'name' => '佐藤花子',
                'email' => 'sato@example.com',
                'birth_date' => '1985-06-15',
                'roles' => 'editor',
                'active' => 1,
            ],
        ];

        $this->databaseMock
            ->expects($this->once())
            ->method('findAll')
            ->with('users')
            ->willReturn($usersData);

        $users = $this->repository->findAll();

        $this->assertCount(2, $users);
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertInstanceOf(User::class, $users[1]);
        $this->assertEquals('田中太郎', $users[0]->getName());
        $this->assertEquals('佐藤花子', $users[1]->getName());
    }

    /**
     * @test
     */
    public function 空の結果が正しく処理されること(): void
    {
        $this->databaseMock
            ->expects($this->once())
            ->method('findAll')
            ->with('users')
            ->willReturn([]);

        $users = $this->repository->findAll();

        $this->assertIsArray($users);
        $this->assertEmpty($users);
    }

    // ===== 管理者検索のテスト =====

    /**
     * @test
     */
    public function 管理者ユーザーのみが取得されること(): void
    {
        $usersData = [
            [
                'name' => '管理者',
                'email' => 'admin@example.com',
                'birth_date' => '1980-01-01',
                'roles' => 'admin,editor',
                'active' => 1,
            ],
            [
                'name' => '一般ユーザー',
                'email' => 'user@example.com',
                'birth_date' => '1990-01-01',
                'roles' => 'viewer',
                'active' => 1,
            ],
        ];

        $this->databaseMock
            ->expects($this->once())
            ->method('findAll')
            ->with('users')
            ->willReturn($usersData);

        $admins = $this->repository->findAdmins();

        $this->assertCount(1, $admins);
        $this->assertEquals('管理者', $admins[0]->getName());
        $this->assertTrue($admins[0]->isAdmin());
    }

    // ===== アクティブユーザー検索のテスト =====

    /**
     * @test
     */
    public function アクティブなユーザーのみが取得されること(): void
    {
        $usersData = [
            [
                'name' => 'アクティブユーザー',
                'email' => 'active@example.com',
                'birth_date' => '1990-01-01',
                'roles' => 'editor',
                'active' => 1,
            ],
            [
                'name' => '非アクティブユーザー',
                'email' => 'inactive@example.com',
                'birth_date' => '1990-01-01',
                'roles' => 'editor',
                'active' => 0,
            ],
        ];

        $this->databaseMock
            ->expects($this->once())
            ->method('findAll')
            ->with('users')
            ->willReturn($usersData);

        $activeUsers = $this->repository->findActive();

        $this->assertCount(1, $activeUsers);
        $this->assertEquals('アクティブユーザー', $activeUsers[0]->getName());
        $this->assertTrue($activeUsers[0]->isActive());
    }

    // ===== ユーザー更新のテスト =====

    /**
     * @test
     */
    public function ユーザーが正しく更新されること(): void
    {
        $this->sampleUser->setName('田中花子');
        $this->sampleUser->setActive(false);

        $expectedData = [
            'name' => '田中花子',
            'birth_date' => '1990-01-01',
            'roles' => 'admin,editor',
            'active' => 0,
        ];

        $this->databaseMock
            ->expects($this->once())
            ->method('update')
            ->with('users', ['email' => 'tanaka@example.com'], $expectedData)
            ->willReturn(true);

        $this->repository->update($this->sampleUser);
        
        $this->assertTrue(true); // 例外が発生しないことを確認
    }

    // ===== ユーザー削除のテスト =====

    /**
     * @test
     */
    public function ユーザーが正しく削除されること(): void
    {
        $this->databaseMock
            ->expects($this->once())
            ->method('delete')
            ->with('users', ['email' => 'tanaka@example.com'])
            ->willReturn(true);

        $this->repository->delete('tanaka@example.com');
        
        $this->assertTrue(true); // 例外が発生しないことを確認
    }

    // ===== メール存在確認のテスト =====

    /**
     * @test
     */
    public function メールアドレスの存在確認ができること(): void
    {
        // 存在する場合
        $this->databaseMock
            ->expects($this->once())
            ->method('find')
            ->with('users', ['email' => 'existing@example.com'])
            ->willReturn(['name' => 'テスト', 'email' => 'existing@example.com', 'birth_date' => '1990-01-01', 'roles' => '', 'active' => 1]);

        $exists = $this->repository->emailExists('existing@example.com');
        $this->assertTrue($exists);
    }

    /**
     * @test
     */
    public function 存在しないメールアドレスでfalseが返されること(): void
    {
        $this->databaseMock
            ->expects($this->once())
            ->method('find')
            ->with('users', ['email' => 'nonexistent@example.com'])
            ->willReturn(null);

        $exists = $this->repository->emailExists('nonexistent@example.com');
        $this->assertFalse($exists);
    }

    // ===== ユーザー数カウントのテスト =====

    /**
     * @test
     */
    public function ユーザー数が正しく取得されること(): void
    {
        $this->databaseMock
            ->expects($this->once())
            ->method('count')
            ->with('users')
            ->willReturn(42);

        $count = $this->repository->count();
        $this->assertEquals(42, $count);
    }

    // ===== 年齢範囲検索のテスト =====

    /**
     * @test
     */
    public function 年齢範囲でユーザーが検索できること(): void
    {
        $currentYear = (int) date('Y');
        
        $usersData = [
            [
                'name' => '25歳',
                'email' => 'user25@example.com',
                'birth_date' => ($currentYear - 25) . '-01-01',
                'roles' => '',
                'active' => 1,
            ],
            [
                'name' => '30歳',
                'email' => 'user30@example.com',
                'birth_date' => ($currentYear - 30) . '-01-01',
                'roles' => '',
                'active' => 1,
            ],
            [
                'name' => '40歳',
                'email' => 'user40@example.com',
                'birth_date' => ($currentYear - 40) . '-01-01',
                'roles' => '',
                'active' => 1,
            ],
        ];

        $this->databaseMock
            ->expects($this->once())
            ->method('findAll')
            ->with('users')
            ->willReturn($usersData);

        // 25歳から35歳までのユーザーを検索
        $users = $this->repository->findByAgeRange(25, 35);

        $this->assertCount(2, $users); // 25歳と30歳のユーザー
        $this->assertEquals('25歳', $users[0]->getName());
        $this->assertEquals('30歳', $users[1]->getName());
    }

    /**
     * @test
     */
    public function 不正な年齢範囲でExceptionが発生すること(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Minimum age cannot be greater than maximum age');

        $this->repository->findByAgeRange(30, 25);
    }

    // ===== キャッシュ機能のテスト =====

    /**
     * @test
     */
    public function キャッシュが正しく動作すること(): void
    {
        $userData = [
            'name' => '田中太郎',
            'email' => 'tanaka@example.com',
            'birth_date' => '1990-01-01',
            'roles' => 'admin',
            'active' => 1,
        ];

        // 最初の検索でデータベースにアクセス
        $this->databaseMock
            ->expects($this->once()) // 1回だけ呼ばれることを期待
            ->method('find')
            ->with('users', ['email' => 'tanaka@example.com'])
            ->willReturn($userData);

        // 1回目の検索
        $user1 = $this->repository->findByEmail('tanaka@example.com');
        
        // 2回目の検索（キャッシュから取得されるはず）
        $user2 = $this->repository->findByEmail('tanaka@example.com');

        $this->assertInstanceOf(User::class, $user1);
        $this->assertInstanceOf(User::class, $user2);
        $this->assertEquals($user1->getName(), $user2->getName());
    }

    /**
     * @test
     */
    public function キャッシュクリア機能が動作すること(): void
    {
        // キャッシュをクリア
        $this->repository->clearCache();
        
        // clearCache メソッドは例外を投げないことを確認
        $this->assertTrue(true);
    }

    // ===== 複雑なシナリオテスト =====

    /**
     * @test
     */
    public function 保存後に検索で同じユーザーが取得できること(): void
    {
        $userData = [
            'name' => '田中太郎',
            'email' => 'tanaka@example.com',
            'birth_date' => '1990-01-01',
            'roles' => 'admin,editor',
            'active' => 1,
        ];

        // 保存処理のモック
        $this->databaseMock
            ->expects($this->once())
            ->method('insert')
            ->with('users', $userData)
            ->willReturn(true);

        // 保存実行
        $this->repository->save($this->sampleUser);

        // 検索処理のモック（保存後はキャッシュから取得されるため、データベースアクセスは発生しない）
        $foundUser = $this->repository->findByEmail('tanaka@example.com');
        
        // キャッシュから取得できることを確認
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals('田中太郎', $foundUser->getName());
    }
}
<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        // Initialisez le noyau de Symfony
        self::bootKernel();

        // Récupérez l'entity manager et le repository depuis le conteneur de services
        $this->entityManager = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->taskRepository = $this->entityManager->getRepository(Task::class);
    }

    public function testFindUncompletedTasks()
    {
        // Créez un utilisateur pour associer aux tâches
        $user = new User();
        $user->setEmail('testuser@example.com');
        $user->setPassword('password'); // En réalité, vous devrez probablement encoder ce mot de passe

        // Persistons l'utilisateur
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Créez des tâches en associant l'utilisateur
        $task1 = new Task();
        $task1->setTitle('Task 1');
        $task1->setDescription('Description Task 1');
        $task1->setDueDate(new \DateTime('+1 day')); // Tâche non terminée
        $task1->setUser($user);

        $task2 = new Task();
        $task2->setTitle('Task 2');
        $task2->setDescription('Description Task 2');
        $task2->setDueDate(new \DateTime('-1 day')); // Tâche terminée
        $task2->setUser($user);

        // Persistons les tâches
        $this->entityManager->persist($task1);
        $this->entityManager->persist($task2);
        $this->entityManager->flush();

        // Appelez la méthode du repository à tester
        $uncompletedTasks = $this->taskRepository->findUncompletedTasks();

        // Vérifiez les résultats
        $this->assertCount(1, $uncompletedTasks); // Vérifiez qu'il n'y a qu'une seule tâche non terminée
        $this->assertContainsOnlyInstancesOf(Task::class, $uncompletedTasks); // Vérifiez que les éléments retournés sont bien des instances de Task
        $this->assertEquals('Task 1', $uncompletedTasks[0]->getTitle()); // Vérifiez que le titre de la tâche non terminée est bien "Task 1"
    }

    public function testSearchTasksByKeyword()
    {
        // Créez un utilisateur pour associer aux tâches
        $user = new User();
        $user->setEmail('testuser@example.com');
        $user->setPassword('password'); // En réalité, vous devrez probablement encoder ce mot de passe

        // Persistons l'utilisateur
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Créez des tâches en associant l'utilisateur
        $task1 = new Task();
        $task1->setTitle('Fix bug in user interface');
        $task1->setDescription('Description Task 1');
        $task1->setUser($user);

        $task2 = new Task();
        $task2->setTitle('Write documentation for API');
        $task2->setDescription('Description Task 2');
        $task2->setUser($user);

        $task3 = new Task();
        $task3->setTitle('Fix issue with database');
        $task3->setDescription('Description Task 3');
        $task3->setUser($user);

        // Persistons les tâches
        $this->entityManager->persist($task1);
        $this->entityManager->persist($task2);
        $this->entityManager->persist($task3);
        $this->entityManager->flush();

        // Appelez la méthode de recherche
        $tasksWithKeyword = $this->taskRepository->searchTasksByKeyword('Fix');

        // Vérifiez les résultats
        $this->assertCount(2, $tasksWithKeyword); // Vérifiez que deux tâches sont retournées
        $this->assertContainsOnlyInstancesOf(Task::class, $tasksWithKeyword); // Vérifiez que les éléments retournés sont bien des instances de Task

        $titles = array_map(fn(Task $task) => $task->getTitle(), $tasksWithKeyword);
        $this->assertContains('Fix bug in user interface', $titles); // Vérifiez que le titre "Fix bug in user interface" est présent
        $this->assertContains('Fix issue with database', $titles); // Vérifiez que le titre "Fix issue with database" est présent
    }

//    protected function tearDown(): void
//    {
//        // Nettoyez la base de données après les tests
//        $this->entityManager->createQuery('DELETE FROM App\Entity\Task')->execute();
//        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
//        $this->entityManager->close();
//        $this->entityManager = null; // Évitez les memory leaks
//    }
}

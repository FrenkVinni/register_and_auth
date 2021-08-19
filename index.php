<?php
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use App\Database;
use App\Authorization;
use App\AuthException;
use App\Session;

require __DIR__ . '/vendor/autoload.php';

$config = include_once 'config/database.php';

$loader = new FilesystemLoader('templates');
$twig = new Environment($loader);

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$session = new Session();
$sessionMiddleware = function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($session)
{
    $session->start();
    $response = $handler->handle($request);
    $session->save();

    return $response;
};
$app->add($sessionMiddleware);

$dsn = $config['dsn'];
$username = $config['username'];
$password = $config['password'];

$database = new Database($dsn, $username, $password);
$authorization = new Authorization($database);

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig){
    $body = $twig->render('index.twig');
    $response->getBody()->write($body);
    return $response;
} );

$app->get('/login', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig){
    $login = $twig->render('login.twig');
    $response->getBody()->write($login);
    return $response;
} );

$app->post('/login-post', function (ServerRequestInterface $request, ResponseInterface $response) {
    $response->getBody()->write('Hello!');
    return $response;
} );

$app->get('/register', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig, $session){
    $register = $twig->render('register.twig', [
        'message'=> $session->flush('massage')
    ]);
    $response->getBody()->write($register);
    return $response;
} );

$app->post('/register-post', function (ServerRequestInterface $request, ResponseInterface $response) use ($authorization, $session){
    $params = (array) $request->getParsedBody();
    try{
        $authorization->register($params);
    }catch (AuthException $exception){
        $session->setData('message', $exception->getMessage());
        return $response->withHeader('Location', '/register')->withStatus(302);
    }
    return $response->withHeader('Location', '/')->withStatus(302);
} );

$app->run();
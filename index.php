<?php

$vendor_directory = getenv('COMPOSER_VENDOR_DIR');
if ($vendor_directory === false) {
	$vendor_directory = __DIR__ . '/vendor';
}
require_once $vendor_directory . '/autoload.php';

/* Initialisations */
$app = require_once 'initapp.php';
require_once 'agvoymodel.php';

/*
 * accueil: on crée la liste des programmations proches dans le futur, et
 * on les affichera dans la section "Actualités".
 */
$app->get ( '/',
	function () use ($app)
	{
		$circuitslist = get_all_circuits();
		$list = array();
		$curtime = date("U");

		foreach ($circuitslist as $circuit) {
			$progs = get_programmations_by_circuit_id($circuit->GetId());
			foreach ($progs as $prog) {
				$progtime = $prog->getDateDepart()->getTimestamp();
				if ($progtime < $curtime) {
					continue;
				}

				if ($progtime - $curtime < 10 * 3600 * 24) {
					$list[] = array($circuit->getId(), $circuit, $prog->getDateDepart()->format('Y-m-d H:i:s'));
					break;
				}
			}
		}

		return $app ['twig']->render ( 'accueil.html.twig', [
				'list' => $list
		] );
	}
)->bind ( 'accueil' );

/*
 * circuitlist: liste tous les circuits
 */
$app->get ( '/circuit',
	function () use ($app)
	{
		$circuitslist = get_all_circuits();

		return $app ['twig']->render ( 'circuitslist.html.twig', [
				'circuitslist' => $circuitslist
		] );
	}
)->bind ( 'circuitlist' );

/*
 * circuitshow: affiche les détails d'un circuit
 */
$app->get ( '/circuit/{id}',
	function ($id) use ($app)
	{
		$circuit = get_circuit_by_id($id);
		$programmations = get_programmations_by_circuit_id($id);
		return $app ['twig']->render ( 'circuitshow.html.twig', [
				'id' => $id,
		        'img' => $circuit->getImage(),
				'circuit' => $circuit
			] );
	}
)->bind ( 'circuitshow' );

/*
 * connexion: page de connexion des utilisateurs
 */
$app->get ( '/connexion',
	function () use ($app)
	{
		$circuitslist = get_all_circuits();

		return $app ['twig']->render ( 'connexion.html.twig', [
				'circuitslist' => $circuitslist
		] );
	}
)->bind ( 'connexion' );

/*
 * programmationlist: liste tous les circuits programmés
 */
$app->get ( '/programmation',
	function () use ($app)
	{
		$programmationslist = get_all_programmations();

		return $app ['twig']->render ( 'programmationslist.html.twig', [
				'programmationslist' => $programmationslist
			] );
	}
)->bind ( 'programmationlist' );

$app->get ( '/backoffice',
	function () use ($app)
	{
		$circuitslist = get_all_circuits();

		return $app ['twig']->render ( 'backoffice/backofficebaselayout.html.twig', [
				'circuitslist' => $circuitslist
		] );
	}
)->bind ( 'backoffice' );

$app->run ();
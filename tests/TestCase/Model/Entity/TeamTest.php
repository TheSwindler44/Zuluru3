<?php
namespace App\Test\TestCase\Model\Entity;

use App\Middleware\ConfigurationLoader;
use App\Model\Entity\Team;
use Cake\Core\Configure;
use Cake\Event\EventList;
use Cake\Event\EventManager;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\Team Test Case
 */
class TeamTest extends TestCase {

	/**
	 * Test subject 1
	 *
	 * @var \App\Model\Entity\Team
	 */
	public $Team1;

	/**
	 * Test subject 2
	 *
	 * @var \App\Model\Entity\Team
	 */

	public $Team2;
	/**
	 * Test subject 2
	 *
	 * @var \App\Model\Entity\Team
	 */
	public $Team3;

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = [
		'app.affiliates',
			'app.users',
				'app.people',
					'app.affiliates_people',
					'app.skills',
			'app.groups',
				'app.groups_people',
			'app.regions',
				'app.facilities',
					'app.fields',
			'app.leagues',
				'app.divisions',
					'app.teams',
						'app.teams_people',
					'app.divisions_days',
					'app.game_slots',
					'app.pools',
						'app.pools_teams',
					'app.games',
			'app.franchises',
				'app.franchises_teams',
			'app.settings',
	];

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		EventManager::instance()->setEventList(new EventList());
		foreach (Configure::read('App.globalListeners') as $listener) {
			EventManager::instance()->on($listener);
		}
		ConfigurationLoader::loadConfiguration();

		$teams = TableRegistry::get('Teams');
		$this->Team1 = $teams->get(TEAM_ID_RED, ['contain' => ['People' => ['Skills'], 'Divisions']]);
		$this->Team2 = $teams->get(TEAM_ID_BLUE, ['contain' => ['People' => ['Skills'], 'Divisions']]);
		$this->Team3 = $teams->get(TEAM_ID_RED_PLAYOFF, ['contain' => ['People' => ['Skills'], 'Divisions']]);
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown() {
		unset($this->Team1);
		unset($this->Team2);
		unset($this->Team3);

		parent::tearDown();
	}

	/**
	 * Test consolidateRoster method
	 *
	 * @return void
	 */
	public function testConsolidateRoster() {
		$this->assertEquals(0, $this->Team1->roster_count);
		$this->assertEquals(0, $this->Team1->skill_count);
		$this->assertEquals(0, $this->Team1->skill_total);
		$this->Team1->consolidateRoster('ultimate');
		$this->assertEquals(2, $this->Team1->roster_count);
		$this->assertEquals(2, $this->Team1->skill_count);
		$this->assertEquals(14, $this->Team1->skill_total);

		$this->assertEquals(0, $this->Team2->roster_count);
		$this->assertEquals(0, $this->Team2->skill_count);
		$this->assertEquals(0, $this->Team2->skill_total);
		$this->Team2->consolidateRoster('ultimate');
		$this->assertEquals(2, $this->Team2->roster_count);
		$this->assertEquals(2, $this->Team2->skill_count);
		$this->assertEquals(7, $this->Team2->skill_total);
	}

	/**
	 * Test twitterName method
	 *
	 * @return void
	 */
	public function testTwitterName() {
		$this->assertEquals('Red @redteam', $this->Team1->twitterName());
		$this->assertEquals('Blue @blueteam', $this->Team2->twitterName());
	}

	/**
	 * Test addGameResult method
	 *
	 * @return void
	 */
	public function testAddGameResult() {
		$this->markTestIncomplete('Not implemented yet. Pretty complex.');
	}
	/**
	 * Test _getRoster();
	 */
	public function testGetRoster() {
		$people = $this->Team1->roster;
		$ids = [];
		foreach ($people as $person) {
			array_push($ids, $person->id);
		}
		$this->assertNotFalse(array_search(PERSON_ID_CAPTAIN, $ids), 'Missing Crystal on roster');
		$this->assertNotFalse(array_search(PERSON_ID_CAPTAIN3, $ids), 'Missing Carolyn on roster');
		$this->assertEquals(2, count($ids), 'Too many people on roster');
	}

	/**
	 * Test _getFullRoster();
	 */
	public function testGetFullRoster() {
		$people = $this->Team1->full_roster;
		$ids = [];
		foreach ($people as $person) {
			array_push($ids, $person->id);
		}
		$this->assertNotFalse(array_search(PERSON_ID_CAPTAIN, $ids), 'Missing Crystal on roster');
		$this->assertNotFalse(array_search(PERSON_ID_CAPTAIN3, $ids), 'Missing Carolyn on roster');
		$this->assertNotFalse(array_search(PERSON_ID_PLAYER, $ids), 'Missing Pam on roster');
		$this->assertEquals(3, count($ids), 'Too many people on roster');
	}

	/**
	 * Test _getAffiliateTeam();
	 */
	public function testGetAffiliatedTeam() {
		$this->assertEquals(null, $this->Team1->affiliated_team);
		$this->assertEquals(TEAM_ID_RED, $this->Team3->affiliated_team->id);
	}

}

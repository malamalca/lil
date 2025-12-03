<?php
/**
 * Pages Controller
 *
 * PHP version 5.3
 *
 * @category Controller
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
namespace Lil\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Lil\Controller\AppController;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @category Controller
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
class PagesController extends AppController
{
    /**
     * IsAuthorized method.
     *
     * @param array $user User data
     *
     * @return bool
     */
    public function isAuthorized($user)
    {
        return true;
    }
    /**
     * Displays a view
     *
     * @return void|\Cake\Network\Response
     * @throws \Cake\Network\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function display()
    {
        $path = func_get_args();

        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            $this->render(implode('/', $path));
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }
    /**
     * Datatables method
     *
     * @return void
     */
    public function datatables()
    {
        $this->autoRender = false;
        $response = $this->response->withType('json');
        $data = $this->render(null, false);
        $response->withStringBody($data);

        return $response;
    }

    /**
     * Dashboard Order method
     *
     * @param array $panelOrder Order of panels
     *
     * @return void
     */
    public function dashboardOrder($panelOrder)
    {
        if (!empty($panelOrder)) {
            $data = [
            'user_id' => $this->currentUser->get('id'),
            'name' => 'Lil.DashboardOrder'
            ];

            $Setting = ClassRegistry::init('Lil.Setting');
            if ($id = $Setting->field('id', $data)) {
                $data['id'] = $id;
            }

            $data['value'] = $panelOrder;

            $Setting->save($data);

            return new CakeResponse();
        }

        return $this->error404();
    }
}

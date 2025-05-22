<?php

namespace Backend\Controllers\Admin\Award;

use Backend\Controllers\Browser\Dashboard\NotFoundController;
use Backend\Controllers\Controller\AFrontendController;
use Backend\Controllers\Controller\TFrontendController;
use Backend\Enums\AwardRequest\EAwardRequestStatus;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\AwardRequest;
use Backend\Template\View;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class IndexController extends AFrontendController
{
    use TFrontendController;
    public string $title = 'Astron Members integrations';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/admin/awards/indexView.php';

    public function view(string $view_method, Request $request, array $params=[], array $pagination=[])
    {
        extract((array) $this);
        extract($params);
        extract($pagination);
        
        $url = site_url().'/admin/awards';
        $view = new View;

        try
        {
            $award_requests = AwardRequest::with('user')->where('status', 'pending')->orderBy('id', 'DESC')
                ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page)->onEachSide(2);

            $info = $award_requests;
            
            $view = View::$view_method($this->indexFile, compact('title', 'context', 'admin', 'award_requests', 'info', 'url'));
        }

        catch (ModelNotFoundException)
        {
            $notfound = new NotFoundController($this->application);
            $view = $notfound->element(new Request);
        }

        return $view;
    }
    
    public function sent(Request $request, $id)
    {
        try 
        {
            $award_request = AwardRequest::findOrFail($id);
            $award_request->status = EAwardRequestStatus::SENT;
            $award_request->answered_at = today();
            $award_request->save();

            $response_data = new ResponseData(['status' => EResponseDataStatus::SUCCESS, 'message' => __('Award request marked as sent.')]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (ModelNotFoundException)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::SUCCESS, 'message' => __('Award request not found.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status);
    }
    
    public function canceled(Request $request, $id)
    {
        try 
        {
            $award_request = AwardRequest::findOrFail($id);
            $award_request->status = EAwardRequestStatus::CANCELED;
            $award_request->answered_at = today();
            $award_request->save();

            $response_data = new ResponseData(['status' => EResponseDataStatus::SUCCESS, 'message' => __('Award request marked as canceled.')]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (ModelNotFoundException)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::SUCCESS, 'message' => __('Award request not found.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status);
    }
}

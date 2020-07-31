<?php

namespace Modules\Cocktail\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Modules\Cocktail\Entities\Conversation;
use Modules\Cocktail\Entities\ConversationUser;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Exceptions\NotExistsException;
use Modules\Cocktail\Http\Requests\ConversationCreateRequest;
use Modules\Cocktail\Http\Requests\ConversationJoinRequest;
use Modules\Cocktail\Http\Requests\EventSpaceAddUserRequest;
use Modules\Cocktail\Http\Requests\EventSpaceRemoveUserRequest;
use Modules\Cocktail\Http\Requests\EventSpaceCreateRequest;
use Modules\Cocktail\Http\Requests\EventSpaceUpdateRequest;
use Modules\Cocktail\Http\Requests\EventSpaceUserRequest;
use Modules\Cocktail\Services\DataService;
use Modules\Cocktail\Services\EventSpaceService;
use Modules\Cocktail\Transformers\ConversationResource;
use Modules\Cocktail\Transformers\EventSpaceConversationResource;
use Modules\Cocktail\Transformers\EventSpaceResource;
use Modules\Cocktail\Transformers\EventSpaceUserResource;
use Nwidart\Modules\Module;

class EventSpaceController extends Controller {

    protected $service;

    public function __construct() {
        $this->service = EventSpaceService::getInstance();
    }

    public function store(EventSpaceCreateRequest $request) {
        DB::connection('tenant')->beginTransaction();
        try {
            $dataService = DataService::getInstance();
            $param = $dataService->prepareSpaceCreateParam($request); // it will prepare array which gonna pass in model
            $event = $this->service->create($param);

            foreach($request->hosts as $host) {
                $param = [
                    $host = [
                        'user_id' => $request->user_id,
                        'space_uuid' => $request->space_uuid,
                        'role' => $request->role,
                        'host' => $request->hosts,
                    ],
                ];
            }
                $event= $this->service->create($param);

            DB::connection('tenant')->commit();
            return (new EventSpaceResource($event))->additional(['status' => TRUE]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(EventSpaceUpdateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $dataService = DataService::getInstance();
            $param = $dataService->prepareSpaceUpdateParam($request); // it will prepare array which gonna pass in model
            $update = $this->service->update($param, $request->space_uuid);
            DB::connection('tenant')->commit();
            return new EventSpaceResource($update);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }

    public function addUserToSpace(EventSpaceAddUserRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $eventSpaceUser = $this->service->addUserToSpace($request->user_id, $request->space_uuid, $request->role);
            DB::connection('tenant')->commit();
            return (new EventSpaceUserResource($eventSpaceUser))->additional(['status' => TRUE]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }

    public function removeUserFromSpace(EventSpaceRemoveUserRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $eventSpaceUsers = $this->service->removeUserFromSpace($request->user_id, $request->space_uuid);
            DB::connection('tenant')->commit();
            return response()->json(['status' => TRUE, 'data' => TRUE,], 200);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }

    public function getEventSpacesForAdmin($eventUuid) {
        try {
            $spaces = EventSpace::where('event_uuid', $eventUuid)->get();
            if (!$spaces)
                throw new Exception();  // to throw the error instead of null so proper message can be shown// to throw exception so that proper msg can be shown
            return EventSpaceResource::collection($spaces)->additional(['status' => TRUE]);
        } catch (Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error '], 500);
        }
    }



    /*
     * END OF ADMIN APIs
    */

    /*
     * USER APIs
     */
    public function getEventSpacesForUser($eventUuid) {
        try {
            $spaces = EventSpace::withCount('spaceUsers')->where('event_uuid', $eventUuid)->get();
            if (!$spaces)
                throw new Exception();  // to throw the error instead of null so proper message can be shown// to throw exception so that proper msg can be shown
            return EventSpaceResource::collection($spaces)->additional(['status' => TRUE]);
        } catch (NotExistsException $e) {
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 422);
        } catch (Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
        }
    }

    public function getSpace($spaceUuid) {
        try {
            $space = EventSpace::with(['conversations' => function ($q) {
                $q->with('users');
                $q->with('ausers');
            }])->where('space_uuid', $spaceUuid)->first();
            if (!$space)
                throw new NotExistsException('space');
            return new EventSpaceConversationResource($space);
        } catch (NotExistsException $e) {
            return response()->json(['status' => FALSE, 'msg' => __('validation.exists', ['attribute' => $e->getMessage()])], 422);
        } catch (Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
        }
    }

    // todo check user is already in with conversation or not
    public function createConversation(ConversationCreateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $conversation = Conversation::create(['space_uuid' => $request->space_uuid]);
            if (!$conversation)
                throw new Exception();
            $this->service->addUserToConversation(Auth::user()->id, $conversation->uuid);
            $this->service->addUserToConversation($request->user_id, $conversation->uuid);
            DB::connection('tenant')->commit();
            return (new ConversationResource($conversation))->additional(['status' => TRUE]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error '], 500);
        }
    }

    /* todo check conversation member count reached or not */
    public function joinConversation(ConversationJoinRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $this->service->addUserToConversation(Auth::user()->id, $request->conversation_id);
            DB::connection('tenant')->commit();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error'], 500);
        }
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\SiteClient;
use App\Models\SiteProject;
use App\Models\SiteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontController extends Controller
{
    public function home(): View
    {
        $services = SiteService::where("is_active", true)->orderBy("display_order")->get();
        $projects = SiteProject::where("is_active", true)->limit(4)->get();
        $clients = SiteClient::where("is_active", true)->orderBy("display_order")->get();
        return view("front.index",
            compact("services", "projects", "clients")
        );
    }

    public function projects(): View
    {
        $projects = SiteProject::where("is_active", true)->paginate(6);
        return view("front.projects", compact("projects"));
    }

    public function projectDetails(): View
    {
        return view("front.project-details");
    }

    public function loadProjectDetails(int $id): JsonResponse
    {
        $details = SiteProject::with("achievements", "sliders")->where("id", $id)->first();
        $response = view("front.project-details", compact("details"))->render();
        return response()->json(['status' => true, "message" => "", "data" => $response]);
    }

    public function services(): View
    {
        $services = SiteService::where("is_active", true)->orderBy("display_order")->get();
        return view("front.services", compact("services"));
    }

    public function about(): View
    {
        $clients = SiteClient::where("is_active", true)->orderBy("display_order")->get();
        return view("front.about", compact("clients"));
    }

    public function contact(): View
    {
        return view("front.contact");
    }

    public function sendMessage(Request $request)
    {
        Message::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ]);
        return redirect()->back()->with("success_message", "Message Sent Successfully");
    }
}
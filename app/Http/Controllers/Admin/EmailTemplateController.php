<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\EmailTemplate;
use App\Models\GlobalLanguage;
use App\Models\EmailTemplateDetails;
use Auth;
use Validator;
use Carbon\Carbon;
use DataTables;
use DB;

class EmailTemplateController extends Controller
{
    public function getmultiLangEmailTemplatesList(Request $request)
    {
        if($request->ajax()) 
        {            
            try 
            {
                DB::statement(DB::raw('set @rownum=0'));

                $email_templates = EmailTemplate::select('id', 'code', 'title','variables','is_active',DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                                ->whereNull('deleted_at')
                                ->orderBy('updated_at','desc')
                                ->get();  
                return Datatables::of($email_templates)->make(true);            
            } 
            catch (\Exception $e) 
            {
                return view('errors.500');
            }            
        }        
        return view('admin.multilang_email_templates.index');
    }

    public function templateAddView()
    {
        $total_languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
                            ->select('global_language.id','alpha2','langEN')
                            ->where('is_deleted',0)
                            ->where('status',1)
                            ->get();

        return view('admin.multilang_email_templates.add',compact('total_languages'));
    }

    public function addEmailTemplate(Request $request)
    {
        $errors = $this->checkAvailableTemplateCode($request);
        if (count($errors) > 0) 
        {
            return redirect()->back()
                ->withErrors(array('message' => $errors))->withInput();
        }
        try
        {
            $email_template = new EmailTemplate;
            $email_template->code = $request->code;
            $email_template->title = $request->title;
            $email_template->variables = $request->variables;
            $email_template->is_active = $request->is_active;
            $email_template->save();

            $languge_ids = $request->lang_id;
        
            foreach($languge_ids as $lang_id)
            {
                $email_template_details = new EmailTemplateDetails;
                $email_template_details->email_template_id = $email_template->id;
                $email_template_details->language_id = $lang_id;
                $email_template_details->subject = $request->subject[$lang_id];
                $email_template_details->value = $request->value[$lang_id];
                $email_template_details->save(); 
            }
            $notification = array(
                'message' => 'Template added successfully!', 
                'alert-type' => 'success'
            );
            return redirect('admin/emailTemplates/list')->with($notification); 
        }
        catch (\Exception $e) 
        {
            return redirect('admin/emailTemplates/list');
        }
    }

    public function templateEditView($id)
    {
        $template = EmailTemplate::findOrFail($id);

        if(!empty($template))
        {
            $template_details = EmailTemplateDetails::where('email_template_id', $id)->get();
            $arr_lang_id = array_column($template_details->toArray(),'language_id');

            $total_languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
                            ->select('global_language.id','alpha2','langEN')
                            ->where('status',1)
                            ->where('is_deleted', 0)
                            ->get();

            return view('admin.multilang_email_templates.edit',compact('template','total_languages','template_details','arr_lang_id'));
        }
    }

    public function updateTemplate(Request $request)
    {
        try
        {
            $email_template = EmailTemplate::findOrFail($request->id);
            if(!empty($email_template))
            {
                $email_template->title = $request->title;
                $email_template->variables = $request->variables;
                $email_template->is_active = $request->is_active;
                $email_template->save();
    
                $languge_ids = $request->lang_id;
                EmailTemplateDetails::where('email_template_id', $request->id)->delete();

                foreach($languge_ids as $lang_id)
                {
                    $email_template_details = new EmailTemplateDetails;
                    $email_template_details->email_template_id = $email_template->id;
                    $email_template_details->language_id = $lang_id;
                    $email_template_details->subject = $request->subject[$lang_id];
                    $email_template_details->value = $request->value[$lang_id];
                    $email_template_details->save(); 
                }
                $notification = array(
                    'message' => 'Template updated successfully!', 
                    'alert-type' => 'success'
                );
                return redirect('admin/emailTemplates/list')->with($notification); 
            } 
        }
        catch (\Exception $e) 
        {
            return redirect('admin/emailTemplates/list');
        }
    }

    protected function checkAvailableTemplateCode($request)
    {
        $templates = EmailTemplate::whereNull('deleted_at')->get()->toArray();

        $errors = array();
        if (count($templates) > 0) 
        {
            $data = $request->all();
            $i = 1;
            foreach ($templates as $template) 
            {
                if (strtolower($template['code']) == strtolower($data['code'])) 
                {
                    $errors[] = 'The code has already been taken.';
                }
            }       
        }

        return $errors;
    }

    public function templateActiveInactive(Request $request)
    {
        try 
        {
            $template = EmailTemplate::where('id',$request->template_id)->first();
            if($request->is_active == 1) 
            {
                $template->is_active = $request->is_active;
                $msg = "Template Activated Successfully!";
            }
            else
            {
                $template->is_active = $request->is_active;
                $msg = "Template Deactivated Successfully!";
            }            
            $template->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        } 
        catch(\Exception $ex) 
        {
            return view('errors.500');            
        }        
    }

    public function deleteTemplate(Request $request)
    {
        $template = EmailTemplate::where('id', $request->template_id)->first();
        if(!empty($template))
        {
            $template->deleted_at = Carbon::now();          
            $template->save();
            $template_details = EmailTemplateDetails::where('email_template_id',$request->template_id)->get();
          
            foreach ($template_details as $detail)
            {
                $detail->deleted_at = Carbon::now();
                $detail->save();
            }
            $result['status'] = 'true';
            $result['msg'] = "Template Deleted Successfully!";
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = "Something went wrong!!";
            return $result;
        }
    }

    public function uploadCKeditorEventTemplateImage(Request $request)
    {
        $folder_name = 'ckeditor-event-template-image';
        uploadCKeditorImage($request, $folder_name);
    }

}
?>
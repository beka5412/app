<?php

namespace Backend\Controllers\User\Product\Checkout\Testimonial;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\User;
use Backend\Exceptions\Checkout\Testimonial\TestimonialNotFoundException;
use Backend\Exceptions\Checkout\Testimonial\EmptyNameException;
use Backend\Exceptions\Checkout\Testimonial\EmptyTextException;
use Backend\Exceptions\Checkout\Testimonial\InvalidImageException;
use Backend\Models\Testimonial;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Exception;

class EditController
{
    public App $application;

    public string $title = 'Editar depoimento';
    public string $context = 'dashboard';
    public User $user;


    public function __construct(App $application)
    {
        $this->application = $application;
        $this->user = user();
    }

    /**
     * Update testimonial
     *
     * @param Request $request
     * @param mixed $product_id
     * @param mixed $checkout_id
     * @param mixed $testimonial_id
     * @throws TestimonialNotFoundException
     * @throws EmptyNameException
     * @throws EmptyTextException
     * @return Response
     */
    public function update(Request $request, mixed $product_id, mixed $checkout_id, mixed $testimonial_id): Response
    {
        $user = $this->user;
        $body = $request->json();
        $name = $body->name ?? '';
        $text = $body->text ?? '';
        $photo = $body->photo ?? '';

        try
        {
            $testimonial = Testimonial::where('id', $testimonial_id)->where('checkout_id', $checkout_id)->where('user_id', $user->id)->first();

            if (empty($testimonial)) throw new TestimonialNotFoundException;
            if (!$name) throw new EmptyNameException;
            if (!$text) throw new EmptyTextException;

            $testimonial->name = $name;
            $testimonial->text = $text;
            if ($photo) $testimonial->photo = $photo;
            $testimonial->save();

            $response_data =
                new ResponseData([
                    'status' => EResponseDataStatus::SUCCESS,
                    'message' => __('Testimonial updated successfully.'),
                    'data' => $testimonial
                ]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (TestimonialNotFoundException $ex)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => __('Testimony not found.')]);
            $response_status = new ResponseStatus('404 Not Found');
        }

        catch (EmptyNameException $ex)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => __('The person\'s name cannot be blank.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (EmptyTextException $ex)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => __('The text of the statement cannot be blank.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (Exception $ex)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => __('Internal error.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status);
    }

    /**
     * Add testimonial
     *
     * @param Request $request
     * @param mixed $product_id
     * @param mixed $checkout_id
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws Exception
     * @return Response
     */
    public function new(Request $request, mixed $product_id, mixed $checkout_id): Response
    {
        $user = $this->user;

        try
        {
            $testimonial = Testimonial::create([
                'user_id' => $user->id,
                'checkout_id' => $checkout_id,
                'name' => __('Draft') . ' ' . time()
            ]);

            $response_data =
                new ResponseData([
                    'status' => EResponseDataStatus::SUCCESS,
                    'message' => __('Testimonial created successfully.'),
                    'data' => $testimonial
                ]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (\Illuminate\Database\Eloquent\MassAssignmentException $ex)
        {
            if (in_debug())
                $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => $ex->getMessage()]);
            else
                $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => __('Error creating testimonial.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (Exception $ex)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => __('Internal error.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status);
    }

    /**
     * Delete testimonial
     *
     * @param Request $request
     * @param mixed $product_id
     * @param mixed $checkout_id
     * @param mixed $testimonial_id
     * @throws TestimonialNotFoundException
     * @throws Exception
     * @return Response
     */
    public function destroy(Request $request, mixed $product_id, mixed $checkout_id, mixed $testimonial_id): Response
    {
        $user = $this->user;

        try
        {
            $testimonial = Testimonial::where('id', $testimonial_id)->where('user_id', $user->id)->where('checkout_id', $checkout_id)->first();
            if (empty($testimonial)) throw new TestimonialNotFoundException;

            $testimonial->delete();

            $response_data = new ResponseData(['status' => EResponseDataStatus::SUCCESS, 'message' => __('Testimonial deleted successfully.')]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (TestimonialNotFoundException $ex)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => __('Testimony not found.')]);
            $response_status = new ResponseStatus('404 Not Found');
        }

        catch (Exception $ex)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => __('Internal error.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status);
    }

    /**
     * Upload checkout image
     * 
     * @access public
     * @param \Backend\Http\Request $request    Request object
     * @param mixed $checkout_id                Checkout ID
     * @param mixed $product_id                 Product ID
     * @throws InvalidImageException
     */
    public function uploadImage(Request $request, $product_id, $checkout_id)
    {
        $user = $this->user;

        $filename = uniqid() . ".png";

        $source = $_FILES['image']['tmp_name'] ?? '';
        $destination = base_path('frontend/public/upload') . "/" . $filename;

        try
        {
            $check = getimagesize($source);
            $mime = $check['mime'] ?? '';
            $is_image = strlen($mime) > 0 && strpos($mime, "image/") >= 0;
            if (!$is_image) throw new InvalidImageException;

            move_uploaded_file($source, $destination);

            $response_data = new ResponseData(['status' => EResponseDataStatus::SUCCESS, 'message' => __('Image loaded successfully.'), "data" => ["image" => "/upload/$filename"]]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (InvalidImageException $ex)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => __('This file is not a valid image.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (Exception $ex)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => __('Internal error.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status);
    }
}

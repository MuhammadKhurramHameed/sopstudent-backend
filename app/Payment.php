<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;
use App;

class Payment extends Model
{
	protected $table = 'payments';

    protected $fillable = [
        'slug', 'item_id', 'item_name', 'user_id', 'start_date', 'end_date', 'plan_type', 'payment_gateway', 'transaction_id', 'paid_by_parent', 'paid_by', 'cost', 'coupon_applied', 'coupon_id', 'actual_cost', 'discount_amount', 'after_discount', 'paid_amount', 'payment_status', 'other_details', 'transaction_record', 'notes', 'admin_comments'
    ];


    public static function getRecordWithSlug($slug)
    {
        return Payment::where('slug', '=', $slug)->first();
    }

    public function updateTransactionRecords($records_type)
    {
        $records = \DB::table('payments')
        ->where('updated_at', '>', 'DATE_SUB(NOW(),INTERVAL -1 HOUR)')
        ->where('payment_status', '=', PAYMENT_STATUS_PENDING);

        if($records_type=='online')
        {
            $records->where('payment_gateway','!=','offline');
        }
        else if($records_type=='offline')
        {
            $records->where('payment_gateway','=','offline');
        }
        else {
            $records->where('user_id','=',$records_type);
        }

        return $records->get();
    }

    /**
     * This method checks the item is purchased or not
     * If purchased, it validates the date is valid to use
     * If valid, it return TRUE
     * ELSE it returns FALSE
     * @param  [type]  $item_id   [description]
     * @param  string  $item_type [description]
     * @param  string  $user_id   [description]
     * @return boolean            [description]
     */
    public static function isItemPurchased($item_id, $item_type = 'combo', $user_id='')
    {
        if($user_id=='')
            $user_id = Auth::user()->id;

            // dd($user_id);

          $date = date('Y-m-d');
        $count = 0;



        $subscription_records = Payment::where('start_date','<=',$date)
                          ->where('end_date','>=',$date)
                          ->where('user_id','=',$user_id)
                          ->where('payment_status','=', 'success')
                          ->get();

        $validity_type = validityType();

        switch ($validity_type) {
            case 'individual':
                foreach($subscription_records as $record)
                {
                    if($record->plan_type == 'combo') {
                       if($item_type == $record->plan_type)
                       {
                            if($item_id == $record->item_id)
                                return TRUE;
                       }

                      if($item_type == 'exam' )
                      {
                         $combo_record = App\ExamSeries::where('id','=',$record->item_id)->first();
                        $combo_data = DB::table('examseries_data')->select('*')
                        ->where('examseries_id','=',$combo_record->id)
                        ->where('quiz_id','=',$item_id)
                        ->get();
                        if($combo_data)
                            return TRUE;
                      }
                    }

                    else if($record->plan_type == 'exam')
                    {
                        if($record->item_id == $item_id ){

                            return TRUE;
                        }
                    }
                    else if($record->plan_type == 'lms')
                    {
                        if($record->item_id == $item_id )
                            return TRUE;
                    }
                }
                break;
            case 'subscriptionfixed':
            case 'subscriptioncustom':
                if ( $subscription_records->count() > 0 ) {
                    return TRUE;
                }
                break;
        }

        return FALSE;

    }

    /**
     * This method returns the overall success, pending and failed records as summary
     * @return [type] [description]
     */
    public function getSuccessFailedCount()
    {
        $data = [];
        $data['success_count']      = Payment::where('payment_status','=','success')->count();
        $data['cancelled_count']    = Payment::where('payment_status','=','cancelled')->count();
        $data['pending_count']      = Payment::where('payment_status','=','pending')->count();
        return $data;
    }

    /**
     * This method gets the overall reports of the payments group by monthly
     * @param  string $year           [description]
     * @param  string $gateway        [description]
     * @param  string $payment_status [description]
     * @return [type]                 [description]
     */
    public function getSuccessMonthlyData($year='', $gateway='',$symbol='=' ,$payment_status='success')
    {
        if($year=='')
            $year = date('Y');

        $query = 'select sum(paid_amount) as total, sum(cost) as cost, MONTHNAME(created_at) as month from payments  where YEAR(created_at) = '.$year.' and payment_status = "'.$payment_status.'" group by YEAR(created_at), MONTH(created_at)';
        if($gateway!='')
        {
            $query = 'select sum(paid_amount) as total, MONTHNAME(created_at) as month from payments  where YEAR(created_at) = '.$year.' and payment_status = "'.$payment_status.'" and payment_gateway '.$symbol.' "'.$gateway.'" group by YEAR(created_at), MONTH(created_at)';
        }

        $result = DB::select($query);

        return $result;
    }

     /**
     * This method checks the item is purchased or not
     * If purchased, it validates the date is valid to use
     * If valid, it return TRUE
     * ELSE it returns FALSE
     * @param  [type]  $item_id   [description]
     * @param  string  $item_type [description]
     * @param  string  $user_id   [description]
     * @return boolean            [description]
     */
    public static function isParentPurchased($item_id, $item_type = 'combo', $user_id='')
    {
        if($user_id=='')
            $user_id = Auth::user()->id;

          $date = date('Y-m-d');
        $count = 0;

        $subscription_records = Payment::where('start_date','<=',$date)
                          ->where('end_date','>=',$date)
                          ->where('user_id','=',$user_id)
                          ->get();

        $validity_type = getSetting('validity_type','site_settings');
        if ( empty( $validity_type ) ) {
            $validity_type = 'individual';
        }

        switch ($validity_type) {
            case 'individual':
                foreach($subscription_records as $record)
                {
                    if($record->plan_type == 'combo') {
                       if($item_type == $record->plan_type)
                       {

                            if($item_id == $record->item_id)
                                return 'purchased';
                       }

                      if($item_type == 'exam' )
                      {
                         $combo_record = App\ExamSeries::where('id','=',$record->item_id)->first();
                        $combo_data = DB::table('examseries_data')->select('*')
                        ->where('examseries_id','=',$combo_record->id)
                        ->where('quiz_id','=',$item_id)
                        ->get();
                        if($combo_data)
                            return 'purchased';
                      }
                    }
                    else if($record->plan_type == 'exam')
                    {
                        if($record->item_id == $item_id ){
                            return 'purchased';
                        }
                    }
                    else if($record->plan_type == 'lms')
                    {
                        if($record->item_id == $item_id ) {
                            return 'purchased';
                        }
                    }
                }
                break;

            case 'subscriptionfixed':
            case 'subscriptioncustom':
                if ( $subscription_records->count() > 0 ) {
                    return 'purchased';
                }
                break;
        }

        return 'notpurchased';
    }

    public function customer() {
      return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function getInvoiceNumberDisplayAttribute($input)
    {
          $invoice_number_format = getSetting('invoice-number-format', 'invoice-settings');
          $invoice_number_separator = getSetting('invoice-number-separator', 'invoice-settings');
          $invoice_number_length = getSetting('invoice-number-length', 'invoice-settings');
          $invoice_no = $this->id;
          $invoice_prefix = getSetting('invoice-prefix', 'invoice-settings');

          $invoice_date = ( $this->start_date ) ? $this->start_date : $this->attributes['start_date'];

          if ( empty( $invoice_date ) ) {
              $invoice_number_format = 'numberbased';
          }

         
          $invoice_no_display = $invoice_no;
          if ( ! empty( $invoice_number_length ) ) {
              $invoice_no = str_pad($invoice_no, $invoice_number_length, 0, STR_PAD_LEFT);
          }
          if ( 'yearbased' === $invoice_number_format ) {
              $invoice_no_display = date('Y', strtotime( $invoice_date ) ) . $invoice_number_separator . $invoice_no;
          } elseif ( 'year2digits' === $invoice_number_format ) {
              $invoice_no_display = date('y', strtotime( $invoice_date ) ) . $invoice_number_separator . $invoice_no;
          } elseif ( 'yearmonthnumber' === $invoice_number_format ) {
              $invoice_no_display = date('Y', strtotime( $invoice_date ) ) . $invoice_number_separator . date('m', strtotime( $invoice_date ) ) . $invoice_number_separator . $invoice_no;
          } elseif ( 'yearbasedright' === $invoice_number_format ) {
              $invoice_no_display = $invoice_no . $invoice_number_separator . date('Y', strtotime( $invoice_date ) );
          } elseif ( 'year2digitsright' === $invoice_number_format ) {
              $invoice_no_display = $invoice_no . $invoice_number_separator . date('y', strtotime( $invoice_date ) );
          } elseif ( 'numbermonthyear' === $invoice_number_format ) {
              $invoice_no_display = $invoice_no . $invoice_number_separator . date('m', strtotime( $invoice_date ) ) . $invoice_number_separator . date('Y', strtotime( $invoice_date ) );
          }
          return $invoice_prefix . $invoice_no_display;
      }
}

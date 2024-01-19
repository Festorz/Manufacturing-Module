public function store(Request $request)
    {
       
        try {
            $request->validate([
                'transaction_date' => 'required',
                'location_id' => 'required',
                'final_total' => 'required'
            ]);

            //Create Production purchase
            $user_id = $request->session()->get('user.id');
            $v_id = $request->input('variation_id');
            $mfg = MfgRecipe::where('id', $v_id)->first();

            // $variation_id = $request->input('variation_id');
            $variation_id = $mfg->variation_id;
            $ingredient = Variation::with('product', 'product_variation', 'product.unit')
                            ->findOrFail($variation_id);
            $final_product_name = $ingredient->product->name;
            $final_product_id = $ingredient->product->id;

            $kgs_packaging_unit = $request->input('kg_units');
            $packets_packaging_unit = $request->input('packet_units');
            $bag_packaging_unit = $request->input('bag_units');
            $bale_packaging_unit = $request->input('bale_units');
            

            
            $transaction_data = $request->only([ 'ref_no', 'transaction_date', 'location_id', 'final_total']);
            $location_id = $transaction_data['location_id'];
            $business_id = $request->session()->get('user.business_id');

            $dust_units = $this->productUtil->num_uf($request->input('mfg_dust_units'));
            $gem_units = $this->productUtil->num_uf($request->input('mfg_gem_units'));
            $bran_units = $this->productUtil->num_uf($request->input('mfg_bran_units'));
            $unga_units = $this->productUtil->num_uf($request->input('mfg_unga_units'));
            $fortified_units = $this->productUtil->num_uf($request->input('mfg_fortified_units'));
            
            // packagings
            $total_half_kgs = $this->productUtil->num_uf($request->input('total_half_kgs'));
            $total_1kgs = $this->productUtil->num_uf($request->input('total_1kgs'));
            $total_2kgs = $this->productUtil->num_uf($request->input('total_2kgs'));
            $total_5kgs = $this->productUtil->num_uf($request->input('total_5kgs'));
            $total_10kgs = $this->productUtil->num_uf($request->input('total_10kgs'));
            $total_25kgs = $this->productUtil->num_uf($request->input('total_25kgs'));
            $total_45kgs = $this->productUtil->num_uf($request->input('total_45kgs'));
            $total_50kgs = $this->productUtil->num_uf($request->input('total_50kgs'));
            $total_90kgs = $this->productUtil->num_uf($request->input('total_90kgs'));
            $total_225kgs = $this->productUtil->num_uf($request->input('total_half_225cost_quantity'));
            
            
            
            
            
            // input amount
            $input_units = $this->productUtil->num_uf($request->input('milling_input'));
            $input_product_id = $this->productUtil->num_uf($request->input('milling_input_id'));

            // unpacked
            
            $unpacked_amount = $this->productUtil->num_uf($request->input('unpacked_quantity'));
            $total_unpacked_cost = $this->productUtil->num_uf($request->input('total_unpacked_cost'));
            
            $carried_fortified_units = $this->productUtil->num_uf($request->input('carried_fortified_units'));
            
            
            // costs
            $total_half_cost = $this->productUtil->num_uf($request->input('total_half_cost'));
            $total_1kg_cost = $this->productUtil->num_uf($request->input('total_1kg_cost'));
            $total_2kg_cost = $this->productUtil->num_uf($request->input('total_2kg_cost'));
            $total_5kg_cost = $this->productUtil->num_uf($request->input('total_5kg_cost'));
            $total_10kg_cost = $this->productUtil->num_uf($request->input('total_10kg_cost'));
            $total_225kg_cost = $this->productUtil->num_uf($request->input('total_half_225cost'));
            $total_25kg_cost = $this->productUtil->num_uf($request->input('total_25kg_cost'));
            $total_45kg_cost = $this->productUtil->num_uf($request->input('total_45kg_cost'));
            $total_50kg_cost = $this->productUtil->num_uf($request->input('total_50kg_cost'));
            $total_90kg_cost = $this->productUtil->num_uf($request->input('total_90kg_cost'));
            
             
            
            
            $single_product_type = "single";
            $combo_product_type = "combo";
            
            // packets
            $total_half_packets = $this->productUtil->num_uf($request->input('total_halfkg_packets'));
            $total_1kgs_packets = $this->productUtil->num_uf($request->input('total_1kg_packets'));
            $total_2kgs_packets = $this->productUtil->num_uf($request->input('total_2kg_packets'));
            
            // packets costs
            $total_half_packets_cost = $this->productUtil->num_uf($request->input('total_half_packets_cost'));
            $total_1kg_packets_cost = $this->productUtil->num_uf($request->input('total_1kg_packets_cost'));
            $total_2kg_packets_cost = $this->productUtil->num_uf($request->input('total_2kg_packets_cost'));
           
            // main
            $production_process = $request->input('production_process');
            
            
            $variation = Variation::where('product_id', $input_product_id)
            ->with(['product'])
            ->first();
            $input_name = $variation->product->name;
            
            $data =  $this->productUtil->getCurrentStock($variation->id, $location_id);
            
            if($production_process == 1){
            
            if($input_units<=$data){        
            
            DB::beginTransaction();  
            
            if($input_units > 0){ 
                self::savedata($request, -$input_units, $input_name, 0, 'Milling', 0, 0,$kgs_packaging_unit);
            }

            if($dust_units >0){
                self::savedata($request, $dust_units, 'MAIZE DUST', 0, '', 0, 0,$kgs_packaging_unit);
            }
            if($gem_units >0){
                self::savedata($request, $gem_units, 'MAIZE GERM', 0, '', 0, 0, $kgs_packaging_unit);
            }
            if($bran_units >0){
                self::savedata($request, $bran_units, 'MAIZE BRAN', 0, '', 0, 0, $kgs_packaging_unit);
            }
            
            // packagings
            if($unpacked_amount >=0){
                $this->productUtil->decreaseProductQuantity($final_product_id, $variation_id, $location_id, $carried_fortified_units, 0, null, false);
                self::savedata($request, $unpacked_amount, $final_product_name, 1, '', $total_unpacked_cost,0, $kgs_packaging_unit);
            }
            if($total_half_packets >0){
                self::savedata($request, $total_half_packets, '1/2KG '.$final_product_name.' PACKET', 0, '', $total_half_packets_cost, 1, $packets_packaging_unit);
            }
            if($total_1kgs_packets >0){
                self::savedata($request, $total_1kgs_packets, '1KG '.$final_product_name.' PACKET', 0, '', $total_1kg_packets_cost,1, $packets_packaging_unit);
            }
            if($total_2kgs_packets >0){
                self::savedata($request, $total_2kgs_packets, '2KG '.$final_product_name.' PACKET', 0, '', $total_2kg_packets_cost,1, $packets_packaging_unit);
            }
            
            if($total_half_kgs >0){
                self::savedata($request, $total_half_kgs, '1/2KG '.$final_product_name, 0, '', $total_half_cost,1, $bale_packaging_unit);
            }
            
            if($total_1kgs >0){
                self::savedata($request, $total_1kgs, '1KG '.$final_product_name, 0, '', $total_1kg_cost, 1, $bale_packaging_unit);
            }
            if($total_2kgs >0){
                self::savedata($request, $total_2kgs, '2KG '.$final_product_name, 0, '', $total_2kg_cost, 1, $bale_packaging_unit);
            }
            if($total_5kgs >0){
                self::savedata($request, $total_5kgs, '5KG '.$final_product_name, 0, '', $total_5kg_cost, 1, $bag_packaging_unit);
            }
            if($total_10kgs >0){
                self::savedata($request, $total_10kgs, '10KG '.$final_product_name, 0, '', $total_10kg_cost, 1, $bag_packaging_unit);
            }
            if($total_25kgs >0){
                self::savedata($request, $total_25kgs, '25KG '.$final_product_name, 0, '', $total_25kg_cost, 1, $bag_packaging_unit);
            }
            if($total_45kgs >0){
                self::savedata($request, $total_45kgs, '45KG '.$final_product_name, 0, '', $total_45kg_cost, 1, $bag_packaging_unit);
            }
            if($total_50kgs >0){
                self::savedata($request, $total_50kgs, '50KG '.$final_product_name, 0, '', $total_50kg_cost ,1, $bag_packaging_unit);
            }
            if($total_90kgs >0){
                self::savedata($request, $total_90kgs, '90KG '.$final_product_name, 0, '', $total_90kg_cost, 1, $bag_packaging_unit);
            }
            if($total_225kgs >0){
                self::savedata($request, $total_225kgs, '22.5KG '.$final_product_name, 0, '', $total_225kg_cost, 1, $bag_packaging_unit);
            }
          
          
            DB::commit();
        
            $output = ['success' => 1,
                            'msg' => __('lang_v1.added_success')
                        ];    
            }else{
                $output = ['success' => 0,
                'msg' => __('Error! Your are input amount for cleaning and milling exceeds current stock. Please update stock')
            ];   
            }
            }
            
            // REPACKAGING
           else if($production_process == 2){        

            DB::beginTransaction();  

            // packagings
            if($unpacked_amount >=0){

                $this->productUtil->decreaseProductQuantity($final_product_id, $variation_id, $location_id, $carried_fortified_units, 0, null, false);
                self::savedata($request, $unpacked_amount, $final_product_name, 1, '', $total_unpacked_cost,0, $kgs_packaging_unit);
            }
            if($total_half_packets >0){
                self::savedata($request, $total_half_packets, '1/2KG '.$final_product_name.' PACKET', 0, '', $total_half_packets_cost, 1, $packets_packaging_unit);
            }
            if($total_1kgs_packets >0){
                self::savedata($request, $total_1kgs_packets, '1KG '.$final_product_name.' PACKET', 0, '', $total_1kg_packets_cost,1, $packets_packaging_unit);
            }
            if($total_2kgs_packets >0){
                self::savedata($request, $total_2kgs_packets, '2KG '.$final_product_name.' PACKET', 0, '', $total_2kg_packets_cost,1, $packets_packaging_unit);
            }
            
            if($total_half_kgs >0){
                self::savedata($request, $total_half_kgs, '1/2KG '.$final_product_name, 0, '', $total_half_cost,1, $bale_packaging_unit);
            }
            
            if($total_1kgs >0){
                self::savedata($request, $total_1kgs, '1KG '.$final_product_name, 0, '', $total_1kg_cost, 1, $bale_packaging_unit);
            }
            if($total_2kgs >0){
                self::savedata($request, $total_2kgs, '2KG '.$final_product_name, 0, '', $total_2kg_cost, 1, $bale_packaging_unit);
            }
            if($total_5kgs >0){
                self::savedata($request, $total_5kgs, '5KG '.$final_product_name, 0, '', $total_5kg_cost, 1, $bag_packaging_unit);
            }
            if($total_10kgs >0){
                self::savedata($request, $total_10kgs, '10KG '.$final_product_name, 0, '', $total_10kg_cost, 1, $bag_packaging_unit);
            }
            if($total_225kgs >0){
                self::savedata($request, $total_225kgs, '22.5KG '.$final_product_name, 0, '', $total_225kg_cost, 1, $bag_packaging_unit);
            }
            if($total_25kgs >0){
                self::savedata($request, $total_25kgs, '25KG '.$final_product_name, 0, '', $total_25kg_cost, 1, $bag_packaging_unit);
            }
            if($total_45kgs >0){
                self::savedata($request, $total_45kgs, '45KG '.$final_product_name, 0, '', $total_45kg_cost, 1, $bag_packaging_unit);
            }
            if($total_50kgs >0){
                self::savedata($request, $total_50kgs, '50KG '.$final_product_name, 0, '', $total_50kg_cost ,1, $bag_packaging_unit);
            }
            if($total_90kgs >0){
                self::savedata($request, $total_90kgs, '90KG '.$final_product_name, 0, '', $total_90kg_cost, 1, $bag_packaging_unit);
            }
          
            DB::commit();
        
            $output = ['success' => 1,
                            'msg' => __('lang_v1.added_success')
                        ];    
            }
       
        } catch (Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        

        }
     return redirect()->action('\Modules\Manufacturing\Http\Controllers\ProductionController@index')->with('status', $output);
    }

    public function savedata ($request, $quantity, $name, $selling, $process, $total, $final_product, $packaging_unit)
    {  


        $business_id = $request->session()->get('user.business_id');
        $manufacturing_settings = $this->mfgUtil->getSettings($business_id);
        $user_id = $request->session()->get('user.id');
        $transaction_data = $request->only([ 'ref_no', 'transaction_date', 'location_id', 'final_total']);
        
        $product_locations = array(
        '0' => "1",
        '1' => "2"
      );

        
        $product = Product::updateOrCreate(
        [
            'name' => $name
        ],
        [
            'type' =>'single',
            'created_by' =>$user_id,
            'process' =>$process,
            'alert_quantity' =>0,
            'enable_stock' =>1,
            'unit_id' =>$packaging_unit,
            'not_for_selling' =>$selling,
            'tax_type' =>'exclusive',                                
            ]                           
            
        );
        $combo_variations = [];
        // if ($product_type == 'combo'){
        //     $combo_variations[] = [
        //                             'variation_id' => $value,
        //                             'quantity' => $this->productUtil->num_uf($quantity[$key]),
        //                             'unit_id' => $unit[$key]
        //                         ];
                
        // }
                            
        $sku = $this->productUtil->generateProductSku($product->id);

        $product->sku = $sku;
        // $product->product_locations()->sync($transaction_data['location_id']);
        $product->save();
        
        // add locations
        // $product->product_locations()->sync($product_locations);
        

        $variation = Variation::where('product_id', $product->id)
                                ->with(['product'])
                                ->first();
        if($variation == null){
        $this->productUtil->createSingleProductVariation($product->id, $product->sku, 0, 0, 0, 0, 0, $combo_variations);
        }
        
                            
                            
        $is_final = !empty($request->input('finalize')) ? 1 : 0;
        $transaction_data['business_id'] = $business_id;
        $transaction_data['created_by'] = $user_id;
        $transaction_data['type'] = 'production_purchase';
        $transaction_data['status'] = $is_final ? 'received' : 'pending';
        $transaction_data['payment_status'] = 'due';
        $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date'], true);
        $transaction_data['final_total'] = $this->productUtil->num_uf($total);

        //Update reference count
        $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
        //Generate reference number
        if (empty($transaction_data['ref_no'])) {
            $prefix = !empty($manufacturing_settings['ref_no_prefix']) ? $manufacturing_settings['ref_no_prefix'] : null;
            $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber($transaction_data['type'], $ref_count, null, $prefix);
        }      

        $variation = Variation::where('product_id', $product->id)
                                ->with(['product'])
                                ->first();
        $final_total = $request->input('final_total');
        // $quantity = $request->input('quantity');
        $waste_units = $this->productUtil->num_uf($request->input('mfg_wasted_units'));
        
        // by_products
       

        $uf_qty = $this->productUtil->num_uf($quantity);
        if (!empty($waste_units)) {
            $new_qty = $uf_qty - $waste_units;
            $uf_qty = $new_qty;
            $quantity = $this->productUtil->num_f($new_qty);
        }

        $final_total_uf = $this->productUtil->num_uf($total);

        $unit_purchase_line_total = 0;
        if ($final_product == 1){
            $unit_purchase_line_total = $final_total_uf / $uf_qty;

        }


        $unit_purchase_line_total_f = $this->productUtil->num_f($unit_purchase_line_total);

        $transaction_data['mfg_wasted_units'] = $waste_units;
        $transaction_data['mfg_production_cost'] = $this->productUtil->num_uf($request->input('production_cost'));
        $transaction_data['mfg_production_cost_type'] = $request->input('mfg_production_cost_type');
        $transaction_data['mfg_is_final'] = $is_final;

        $purchase_line_data = [
            'variation_id' => $variation->id,
            'quantity' => $quantity,
            'product_id' => $variation->product_id,
            'product_unit_id' => $packaging_unit,
            'pp_without_discount' => $unit_purchase_line_total_f,
            'discount_percent' => 0,
            'purchase_price' => $unit_purchase_line_total_f,
            'purchase_price_inc_tax' => $unit_purchase_line_total_f,
            'item_tax' => 0,
            'purchase_line_tax_id' => null,
            'mfg_date' => $this->transactionUtil->format_date($transaction_data['transaction_date'])
        ];
        if (request()->session()->get('business.enable_lot_number') == 1) {
            $purchase_line_data['lot_number'] = $request->input('lot_number');
        }

        if (request()->session()->get('business.enable_product_expiry') == 1) {
            $purchase_line_data['exp_date'] = $request->input('exp_date');
        }

        if (!empty($request->input('sub_unit_id'))) {
            $purchase_line_data['sub_unit_id'] = $packaging_unit;
        }
     

        $transaction = Transaction::create($transaction_data);

        Media::uploadMedia($business_id, $transaction, $request, 'documents', false);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $update_product_price = !empty($manufacturing_settings['enable_updating_product_price']) && $is_final ? true : false;

        $this->productUtil->createOrUpdatePurchaseLines($transaction, [$purchase_line_data], $currency_details, $update_product_price);

        //Adjust stock over selling if found
        $this->productUtil->adjustStockOverSelling($transaction);

        //Create production sell
        $transaction_sell_data = [
            'business_id' => $business_id,
            'location_id' => $transaction->location_id,
            'transaction_date' => $transaction->transaction_date,
            'created_by' => $transaction->created_by,
            'status' => $is_final ? 'final' : 'draft',
            'type' => 'production_sell',
            'mfg_parent_production_purchase_id' => $transaction->id,
            'payment_status' => 'due',
            'final_total' => $transaction->final_total
        ];

        $sell_lines = [];
        $ingredient_quantities = !empty($request->input('ingredients')) ? $request->input('ingredients') : [];

        //Get ingredient details to create sell lines
        $variation_id = $request->input('variation_id');

        $recipe = MfgRecipe::where('id', $variation_id)->first();

        $all_variation_details = $this->mfgUtil->getIngredientDetails($recipe, $business_id);

        foreach ($all_variation_details as $variation_details) {
            $variation = $variation_details['variation'];
            
            $line_sub_unit_id = !empty($ingredient_quantities[$variation_details['id']]['sub_unit_id']) ?
            $ingredient_quantities[$variation_details['id']]['sub_unit_id'] : null;

            $line_multiplier = !empty($line_sub_unit_id) ? $variation_details['sub_units'][$line_sub_unit_id]['multiplier'] : 1;

            $mfg_waste_percent = !empty($ingredient_quantities[$variation_details['id']]['mfg_waste_percent']) ? $this->productUtil->num_uf($ingredient_quantities[$variation_details['id']]['mfg_waste_percent']) : 0;

            $mfg_ingredient_group_id = !empty($ingredient_quantities[$variation_details['id']]['mfg_ingredient_group_id']) ? $ingredient_quantities[$variation_details['id']]['mfg_ingredient_group_id'] : null;

            $sell_lines[] = [
                    'product_id' => $variation->product_id,
                    'variation_id' => $variation->id,
                    'quantity' => $this->productUtil->num_uf($ingredient_quantities[$variation_details['id']]['quantity']),
                    'item_tax' => 0,
                    'tax_id' => null,
                    'unit_price' => $variation->dpp_inc_tax * $line_multiplier,
                    'unit_price_inc_tax' => $variation->dpp_inc_tax * $line_multiplier,
                    'enable_stock' => $variation_details['enable_stock'],
                    'product_unit_id' => $packaging_unit,
                    'sub_unit_id' => $packaging_unit,
                    'base_unit_multiplier' => $line_multiplier,
                    'mfg_waste_percent' => $mfg_waste_percent,
                    'mfg_ingredient_group_id' => $mfg_ingredient_group_id
                ];
        }

        //Create Sell Transfer transaction
        $production_sell = Transaction::create($transaction_sell_data);
        // dd($production_sell);

        // if (!empty($sell_lines)) {
        //     $this->transactionUtil->createOrUpdateSellLines($production_sell, $sell_lines, $transaction_sell_data['location_id'], 
        //     null, null, ['mfg_waste_percent' => 'mfg_waste_percent', 'mfg_ingredient_group_id' => 'mfg_ingredient_group_id']);
        // }

        if ($production_sell->status == 'final') {
            foreach ($sell_lines as $sell_line) {
                if ($sell_line['enable_stock']) {
                    $line_qty = $sell_line['quantity'] * $sell_line['base_unit_multiplier'];
                    // $this->productUtil->decreaseProductQuantity(
                    //     $sell_line['product_id'],
                    //     $sell_line['variation_id'],
                    //     $production_sell->location_id,
                    //     $line_qty
                    // );
                }
            }

            $business_details = $this->businessUtil->getDetails($business_id);
            $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

            //Map sell lines with purchase lines
            $business = ['id' => $business_id,
                        'accounting_method' => $request->session()->get('business.accounting_method'),
                        'location_id' => $production_sell->location_id,
                        'pos_settings' => $pos_settings
                    ];
            $this->transactionUtil->mapPurchaseSell($business, $production_sell->sell_lines, 'production_purchase');
        }

        
    }

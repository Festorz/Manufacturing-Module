try {
            $user_id = $request->session()->get('user.id');

            $input = $request->only(['variation_id','r_id','ig_index','input_id','ingredient_groups','mfg_ingredient_group_id', 
            'ingredients', 'total', 'instructions','process','ingredient_line_id','ingredients_cost','milling_quantity',
             'dust_quantity', 'dust_percent', 'gem_quantity', 'gem_percent', 
                'bran_quantity', 'bran_percent', 'unga_quantity','unga_percent', 'premix_quantity','fortified_unga','extra_cost',
                'fortify_quantity','premix_quota','total_quantity', 'total', 'extra_cost', 'milling_cost', 'fortifying_cost',
                 'electricity_cost', 'electricity_cost_type','total_electricity_cost','labourers_no','labor_cost','total_labour_bills', 'others',
                 'production_cost_type', 'maize_offload_quantity', 'maize_offload_quantity_cost', 'total_maize_offload_quantity_cost',
                 'bales_no', 'bale_cost', 'total_bale_bills', 'kg1packets', 'kg1packet_cost','kg2packets',
                 'kg2packet_cost', 'packed_bales', 'bale_pack_cost','total_bale_pack_cost',
                 'bags', 'bag_cost', 'balew', 'balew_cost', 'total_packaging_bills', 'transport_cost']);
            
                // dd($input);
            $form_fields = ['name', 'brand_id', 'unit_id', 'category_id', 'tax', 'type', 'barcode_type', 'sku', 'alert_quantity', 'tax_type', 'weight', 'product_custom_field1', 'product_custom_field2', 'product_custom_field3', 'product_custom_field4', 'product_description', 'sub_unit_ids'];


            
                if (!empty($input['ingredients'])) {
                $variation = Variation::findOrFail($input['variation_id']);

                if($input['r_id']){
                $recipe = MfgRecipe::updateOrCreate(
                    [
                        'id' => $input['r_id'],
                    ],
                    [
                        'variation_id' => $input['variation_id'],
                        'product_id' => $variation->product_id,
                        'final_price' => $this->moduleUtil->num_uf($input['total']),
                        'ingredients_cost' => $input['ingredients_cost'],
                        'electricity_cost' => $input['electricity_cost'],
                        'electricity_cost_type' => $input['electricity_cost_type'],
                        'total_electricity_cost' => $input['total_electricity_cost'],
                        'labourers_no' => $input['labourers_no'],
                        'labor_cost' => $input['labor_cost'],
                        'total_labour_bills' => $input['total_labour_bills'],
                        'maize_offload_quantity' => $input['maize_offload_quantity'],
                        'maize_offload_quantity_cost' => $input['maize_offload_quantity_cost'],
                        'total_maize_offload_quantity_cost' => $input['total_maize_offload_quantity_cost'],
                       
                        'bales_no' => $input['bales_no'],
                        'bale_cost' => $input['bale_cost'],
                        'total_bale_bills' => $input['total_bale_bills'],
                        'packed_bales' => $input['packed_bales'],
                        'bale_pack_cost' => $input['bale_pack_cost'],
                        'total_bale_pack_cost' => $input['total_bale_pack_cost'],
                        'kg1packets' => $input['kg1packets'],
                        'kg1packet_cost' => $input['kg1packet_cost'],
                        'kg2packets' => $input['kg2packets'],
                        'kg2packet_cost' => $input['kg2packet_cost'],
                        'bags' => $input['bags'],
                        'bag_cost' => $input['bag_cost'],
                        'balew' => $input['balew'],
                        'balew_cost' => $input['balew_cost'],
                        'total_packaging_bills' => $input['total_packaging_bills'],
                        'transport_cost' => $input['transport_cost'],
                        'others' => $input['others'],
                        'extra_cost' => $input['extra_cost'],
                        'production_cost_type' => 'fixed',
                        'total_quantity' => $this->moduleUtil->num_uf($input['total_quantity']),
                        'instructions' => $input['instructions'],
                        'sub_unit_id' => !empty($request->input('sub_unit_id')) ? $request->input('sub_unit_id') : null
                    ]
                );
                    
                }else{
                $recipe = MfgRecipe::create(
                    // [
                    // ],
                    [
                        'variation_id' => $input['variation_id'],
                        'product_id' => $variation->product_id,
                        'final_price' => $this->moduleUtil->num_uf($input['total']),
                        'ingredients_cost' => $input['ingredients_cost'],
                        'electricity_cost' => $input['electricity_cost'],
                        'electricity_cost_type' => $input['electricity_cost_type'],
                        'total_electricity_cost' => $input['total_electricity_cost'],
                        'labourers_no' => $input['labourers_no'],
                        'labor_cost' => $input['labor_cost'],
                        'total_labour_bills' => $input['total_labour_bills'],
                        'maize_offload_quantity' => $input['maize_offload_quantity'],
                        'maize_offload_quantity_cost' => $input['maize_offload_quantity_cost'],
                        'total_maize_offload_quantity_cost' => $input['total_maize_offload_quantity_cost'],
                        
                        'bales_no' => $input['bales_no'],
                        'bale_cost' => $input['bale_cost'],
                        'total_bale_bills' => $input['total_bale_bills'],
                        'packed_bales' => $input['packed_bales'],
                        'bale_pack_cost' => $input['bale_pack_cost'],
                        'total_bale_pack_cost' => $input['total_bale_pack_cost'],
                        'kg1packets' => $input['kg1packets'],
                        'kg1packet_cost' => $input['kg1packet_cost'],
                        'kg2packets' => $input['kg2packets'],
                        'kg2packet_cost' => $input['kg2packet_cost'],
                        'bags' => $input['bags'],
                        'bag_cost' => $input['bag_cost'],
                        'balew' => $input['balew'],
                        'balew_cost' => $input['balew_cost'],
                        'total_packaging_bills' => $input['total_packaging_bills'],
                        'transport_cost' => $input['transport_cost'],
                        'others' => $input['others'],
                        'extra_cost' => $input['extra_cost'],
                        'production_cost_type' => 'fixed',
                        'total_quantity' => $this->moduleUtil->num_uf($input['total_quantity']),
                        'instructions' => $input['instructions'],
                        'sub_unit_id' => !empty($request->input('sub_unit_id')) ? $request->input('sub_unit_id') : null
                    ]
                );
                    
                }
                

                $ingredients = [];
                $edited_ingredients = [];
                $ingredient_groups = $request->input('ingredient_groups');
                $ingredient_group_descriptions = $request->input('ingredient_group_description');
                $created_ig_groups = [];


                
                foreach ($input['ingredients'] as $key => $value) {

                    $variation = Variation::with(['product'])
                                        ->findOrFail($value['ingredient_id']);

                    if (!empty($value['ingredient_line_id'])) {
                        $ingredient = MfgRecipeIngredient::find($value['ingredient_line_id']);
                        $edited_ingredients[] = $ingredient->id;
                    } else {
                        $ingredient = new MfgRecipeIngredient(['variation_id' => $value['ingredient_id']]);
                    }

                    // product id
                    $ingredient->input_id = $this->moduleUtil->num_uf(!empty($value['input_id'])?$value['input_id']:0);

                    // quantity
                    $ingredient->milling_quantity = $this->moduleUtil->num_uf(!empty($value['milling_quantity'])?$value['milling_quantity']:0);
                    $ingredient->fortify_quantity = $this->moduleUtil->num_uf(!empty($value['fortify_quantity'])?$value['fortify_quantity']:0);
                    
                    // cost
                    $ingredient->milling_cost = $this->moduleUtil->num_uf(!empty($value['milling_cost'])?$value['milling_cost']:0);
                    $ingredient->fortifying_cost = $this->moduleUtil->num_uf(!empty($value['fortifying_cost'])?$value['fortifying_cost']:0);

                    // dust
                    $ingredient->dust = $this->moduleUtil->num_uf(!empty($value['dust_quantity'])?$value['dust_quantity']:0);
                    $ingredient->dust_percent = $this->moduleUtil->num_uf(!empty($value['dust_percent'])?$value['dust_percent']:0);

                     // gem
                    $ingredient->gem = $this->moduleUtil->num_uf(!empty($value['gem_quantity'])?$value['gem_quantity']:0);
                    $ingredient->gem_percent = $this->moduleUtil->num_uf(!empty($value['gem_percent'])?$value['gem_percent']:0);
                     
                      // bran
                    $ingredient->bran = $this->moduleUtil->num_uf(!empty($value['bran_quantity'])?$value['bran_quantity']:0);
                    $ingredient->bran_percent = $this->moduleUtil->num_uf(!empty($value['bran_percent'])?$value['bran_percent']:0);


                     // unga
                    $ingredient->unga = $this->moduleUtil->num_uf(!empty($value['unga_quantity'])?$value['unga_quantity']:0);
                    $ingredient->unga_percent = $this->moduleUtil->num_uf(!empty($value['unga_percent'])?$value['unga_percent']:0);

                      // fortified
                    $ingredient->fortified_unga = $this->moduleUtil->num_uf(!empty($value['fortified_unga'])?$value['fortified_unga']:0);


                      // premix
                    $ingredient->premix = $this->moduleUtil->num_uf(!empty($value['premix_quantity'])?$value['premix_quantity']:0);
                    $ingredient->premix_quota = $this->moduleUtil->num_uf(!empty($value['premix_quota'])?$value['premix_quota']:0);


                    $ingredient->sub_unit_id = !empty($value['sub_unit_id']) && $value['sub_unit_id'] != $variation->product->unit_id ? $value['sub_unit_id'] : null;

                    


                    if (isset($value['ig_index'])) {
                        $ig_name = $ingredient_groups[$value['ig_index']];
                        $ig_description = $ingredient_group_descriptions[$value['ig_index']];

                        //Create ingredient group if not created already
                        if (!empty($created_ig_groups[$value['ig_index']])) {
                            $ingredient_group = $created_ig_groups[$value['ig_index']];
                        } elseif (empty($value['mfg_ingredient_group_id'])) {
                            $ingredient_group = MfgIngredientGroup::create(
                                [
                                    'name' => $ig_name,
                                    'business_id' => $business_id,
                                    'description' => $ig_description
                                ]
                            );
                        } else {
                            $ingredient_group = MfgIngredientGroup::where('business_id', $business_id)
                                                                ->find($value['mfg_ingredient_group_id']);
                            if ($ingredient_group->name != $ig_name || $ingredient_group->description != $ig_description) {
                                $ingredient_group->name = $ig_name;
                                $ingredient_group->description = $ig_description;
                                $ingredient_group->save();
                            }

                            $ingredient_group = MfgIngredientGroup::firstOrNew(
                                ['business_id' => $business_id, 'id' => $value['mfg_ingredient_group_id']],
                                ['name' => $ig_name, 'description' => $ig_description]
                            );
                        }

                        $created_ig_groups[$value['ig_index']] = $ingredient_group;

                        $ingredient->mfg_ingredient_group_id = $ingredient_group->id;
                    }

                    $ingredients[] = $ingredient;
                }
                if (!empty($edited_ingredients)) {
                    MfgRecipeIngredient::where('mfg_recipe_id', $recipe->id)
                                                ->whereNotIn('id', $edited_ingredients)
                                                ->delete();
                }

                $recipe->ingredients()->saveMany($ingredients);
            }
            $output = ['success' => 1,
                            'msg' => __('lang_v1.added_success')
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

<?php
/**
 * CHtml class files.
 * 
 * 
 * @author samson.zhou<samson.zhou@newbiiz.com>
 * @date 2010-08-18
 */
class CHtml
{
	public static $errorCss = 'errorbo';
	
	/**
	 * get error info from model
	 * 
	 * @param CModel $model
	 * @return string
	 * @see CModel
	 */
	public static function errorModelSummery( $model , $attribute = null )
	{
		if( is_null( $attribute ) )
			return self::errorSummery( $model->getErrors() );
		else
		{
			$aryError = $model->getError($attribute , false);
			return empty( $aryError ) ? '' : self::errorSummery( array( $aryError ) );
		}
	}
	
/**
	 * get error info
	 * 
	 * @param Array $_aryError
	 * @return string
	 */
	public static function errorSummery( $_aryError = array() )
	{
		$returnHtml = "";
		if( !empty( $_aryError ) )
		{
			$returnHtml = "<div class=\"error\">";
			//$returnHtml .= "<p>Please check these error:</p>";
			$returnHtml .= "<ul>";
			foreach( (array)$_aryError as $k=>$v )
			{
				if( !empty($k) )				
					$returnHtml .= "<li>".implode('&nbsp;&nbsp;', $v)."</li>";
				else
					$returnHtml .= "<li>".implode('&nbsp;&nbsp;', $v)."</li>";
			}
			$returnHtml .= "</ul>";
			$returnHtml .= "</div>";
		}
		return $returnHtml;
	}
	
	/**
	 * Appends {@link errorCss} to the 'class' attribute.
	 * @param array HTML options to be modified
	 */
	protected static function addErrorCss(&$htmlOptions)
	{
		if(isset($htmlOptions['class']))
			$htmlOptions['class'].=' '.self::$errorCss;
		else
			$htmlOptions['class']=self::$errorCss;
	}
	
	/**
	 * Generates a valid HTML ID based on name.
	 * @param string $name name from which to generate HTML ID
	 * @return string the ID generated based on name.
	 */
	public static function getIdByName($name)
	{
		return str_replace(array('[]','][','[',']',' '),array('','_','_','','_'),$name);
	}
	
	/**
	 * Generates a label tag.
	 * @param string $label label text. Note, you should HTML-encode the text if needed.
	 * @param string $for the ID of the HTML element that this label is associated with.
	 * If this is false, the 'for' attribute for the label tag will not be rendered.
	 * @param array $htmlOptions additional HTML attributes.
	 * The following HTML option is recognized:
	 * <ul>
	 * <li>required: if this is set and is true, the label will be styled
	 * with CSS class 'required' (customizable with CHtml::$requiredCss),
	 * and be decorated with {@link CHtml::beforeRequiredLabel} and
	 * {@link CHtml::afterRequiredLabel}.</li>
	 * </ul>
	 * @return string the generated label tag
	 */
	public static function label($label,$for,$htmlOptions=array())
	{
		if($for===false)
			unset($htmlOptions['for']);
		else
			$htmlOptions['for']=$for;
		if(isset($htmlOptions['required']))
		{
			if($htmlOptions['required'])
			{
				if(isset($htmlOptions['class']))
					$htmlOptions['class'].=' '.self::$requiredCss;
				else
					$htmlOptions['class']=self::$requiredCss;
				$label=self::$beforeRequiredLabel.$label.self::$afterRequiredLabel;
			}
			unset($htmlOptions['required']);
		}
		return self::tag('label',$htmlOptions,$label);
	}
	
	/**
	 * Generates an HTML element.
	 * @param string the tag name
	 * @param array the element attributes. The values will be HTML-encoded using {@link encode()}.
	 * Since version 1.0.5, if an 'encode' attribute is given and its value is false,
	 * the rest of the attribute values will NOT be HTML-encoded.
	 * @param mixed the content to be enclosed between open and close element tags. It will not be HTML-encoded.
	 * If false, it means there is no body content.
	 * @param boolean whether to generate the close tag.
	 * @return string the generated HTML element tag
	 */
	public static function tag($tag,$htmlOptions=array(),$content=false,$closeTag=true)
	{
		$html='<' . $tag . self::renderAttributes($htmlOptions);
		if($content===false)
			return $closeTag ? $html.' />' : $html.'>';
		else
			return $closeTag ? $html.'>'.$content.'</'.$tag.'>' : $html.'>'.$content;
	}
	
	/**
	 * Generates an open HTML element.
	 * @param string the tag name
	 * @param array the element attributes. The values will be HTML-encoded using {@link encode()}.
	 * Since version 1.0.5, if an 'encode' attribute is given and its value is false,
	 * the rest of the attribute values will NOT be HTML-encoded.
	 * @return string the generated HTML element tag
	 */
	public static function openTag($tag,$htmlOptions=array())
	{
		return '<' . $tag . self::renderAttributes($htmlOptions) . '>';
	}

	/**
	 * Generates a close HTML element.
	 * @param string the tag name
	 * @return string the generated HTML element tag
	 */
	public static function closeTag($tag)
	{
		return '</'.$tag.'>';
	}

	/**
	 * Encloses the given string within a CDATA tag.
	 * @param string the string to be enclosed
	 * @return string the CDATA tag with the enclosed content.
	 */
	public static function cdata($text)
	{
		return '<![CDATA[' . $text . ']]>';
	}
	
/**
	 * Encloses the given CSS content with a CSS tag.
	 * @param string the CSS content
	 * @param string the media that this CSS should apply to.
	 * @return string the CSS properly enclosed
	 */
	public static function css($text,$media='')
	{
		if($media!=='')
			$media=' media="'.$media.'"';
		return "<style type=\"text/css\"{$media}>\n/*<![CDATA[*/\n{$text}\n/*]]>*/\n</style>";
	}

	/**
	 * Links to the specified CSS file.
	 * @param string the CSS URL
	 * @param string the media that this CSS should apply to.
	 * @return string the CSS link.
	 */
	public static function cssFile($url,$media='')
	{
		if($media!=='')
			$media=' media="'.$media.'"';
		return '<link rel="stylesheet" type="text/css" href="'.self::encode($url).'"'.$media.' />';
	}

	/**
	 * Encloses the given JavaScript within a script tag.
	 * @param string the JavaScript to be enclosed
	 * @return string the enclosed JavaScript
	 */
	public static function script($text)
	{
		return "<script type=\"text/javascript\">\n/*<![CDATA[*/\n{$text}\n/*]]>*/\n</script>";
	}

	/**
	 * Includes a JavaScript file.
	 * @param string URL for the JavaScript file
	 * @return string the JavaScript file tag
	 */
	public static function scriptFile($url)
	{
		return '<script type="text/javascript" src="'.self::encode($url).'"></script>';
	}

	/**
	 * Generates an opening form tag.
	 * This is a shortcut to {@link beginForm}.
	 * @param mixed the form action URL (see {@link normalizeUrl} for details about this parameter.)
	 * @param string form method (e.g. post, get)
	 * @param array additional HTML attributes (see {@link tag}).
	 * @return string the generated form tag.
	 */
	public static function form($action='',$method='post',$htmlOptions=array())
	{
		return self::beginForm($action,$method,$htmlOptions);
	}

	/**
	 * Generates an opening form tag.
	 * Note, only the open tag is generated. A close tag should be placed manually
	 * at the end of the form.
	 * @param mixed the form action URL (see {@link normalizeUrl} for details about this parameter.)
	 * @param string form method (e.g. post, get)
	 * @param array additional HTML attributes (see {@link tag}).
	 * @return string the generated form tag.
	 * @since 1.0.4
	 * @see endForm
	 */
	public static function beginForm($action='',$method='post',$htmlOptions=array())
	{
		$htmlOptions['action']=self::normalizeUrl($action);
		$htmlOptions['method']=$method;
		$form=self::tag('form',$htmlOptions,false,false);
		return $form;
	}

	/**
	 * Generates a closing form tag.
	 * @return string the generated tag
	 * @since 1.0.4
	 * @see beginForm
	 */
	public static function endForm()
	{
		return '</form>';
	}
	
	/**
	 * Generates a hyperlink tag.
	 * @param string link body. It will NOT be HTML-encoded. Therefore you can pass in HTML code such as an image tag.
	 * @param mixed a URL or an action route that can be used to create a URL.
	 * See {@link normalizeUrl} for more details about how to specify this parameter.
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} and {@link tag} for more details.)
	 * @return string the generated hyperlink
	 * @see normalizeUrl
	 * @see clientChange
	 */
	public static function link($text,$url='#',$htmlOptions=array())
	{
		if($url!=='')
			$htmlOptions['href']=self::normalizeUrl($url);
		//self::clientChange('click',$htmlOptions);
		return self::tag('a',$htmlOptions,$text);
	}
	
	/**
	 * Generates an image tag.
	 * @param string the image URL
	 * @param string the alternative text display
	 * @param array additional HTML attributes (see {@link tag}).
	 * @return string the generated image tag
	 */
	public static function image($src,$alt='',$htmlOptions=array())
	{
		$htmlOptions['src']=$src;
		$htmlOptions['alt']=$alt;
		return self::tag('img',$htmlOptions);
	}
	
	public static function activeTextField( $_model , $_field , $_htmlOptions = array() )	
	{
		return self::activeInputField('text',$_model,$_field,$_htmlOptions);		
	}
	
	public static function activeHiddenField( $_model , $_field, $htmlOptions=array() )
	{
		return self::activeInputField('hidden',$_model,$_field,$htmlOptions);
	}
	
	public static function activePasswordField($_model,$_attribute,$_htmlOptions=array())
	{
		return self::activeInputField('password',$_model,$_attribute,$_htmlOptions);
	}
	
	public static function activeTextArea($_model,$_attribute,$_htmlOptions=array())	
	{
		$name = get_class($_model)."[{$_attribute}]";
		
		if($_model->hasError($_attribute))
			self::addErrorCss($_htmlOptions);
			
		$_htmlOptions['name'] = get_class( $_model )."[{$_attribute}]";
		//return self::tag('textarea',$_htmlOptions);		
		if(isset($_htmlOptions['value']))
		{
			$text=$_htmlOptions['value'];
			unset($_htmlOptions['value']);
		}
		else
			$text = $_model->getData( $_attribute );
		return self::tag('textarea',$_htmlOptions,isset($_htmlOptions['encode']) && !$_htmlOptions['encode'] ? $text : self::encode($text));
	}
	
	public static function activeRadioButton($_model,$_attribute,$_htmlOptions=array())
	{
		$value = self::encode( $_model->getData( $_attribute ) );
		if(!isset($_htmlOptions['value']))
			$_htmlOptions['value']=1;
		if(!isset($_htmlOptions['checked']) && $value==$_htmlOptions['value'])
			$_htmlOptions['checked']='checked';
		
		return self::activeInputField('radio',$_model,$_attribute,$_htmlOptions);
	}

	public static function activeCheckBox($_model,$_attribute,$_htmlOptions=array())
	{
		$value = self::encode( $_model->getData( $_attribute ) );
		if(!isset($_htmlOptions['value']))
			$_htmlOptions['value']=1;
		if(!isset($_htmlOptions['checked']) && $value==$_htmlOptions['value'])
			$_htmlOptions['checked']='checked';
		
		return self::activeInputField('checkbox',$_model,$_attribute,$_htmlOptions);
	}

	public static function activeDropDownList($_model,$_attribute,$_data,$_htmlOptions=array())
	{
		$value = self::encode( $_model->getData( $_attribute ) );
		if($_model->hasError( $_attribute) )
			self::addErrorCss( $_htmlOptions );
		$_htmlOptions['name'] = get_class( $_model )."[{$_attribute}]";
		$options="\n".self::listOptions($value,$_data,$_htmlOptions);
		if(isset($_htmlOptions['multiple']))
		{
			if(substr($_htmlOptions['name'],-2)!=='[]')
				$_htmlOptions['name'].='[]';
		}
		return self::tag('select',$_htmlOptions,$options);
	}

	public static function activeListBox($name,$select,$data,$htmlOptions=array())
	{
		if(!isset($htmlOptions['size']))
			$htmlOptions['size']=4;
		if(isset($htmlOptions['multiple']))
		{
			if(substr($name,-2)!=='[]')
				$name.='[]';
		}
		return self::dropDownList($name,$select,$data,$htmlOptions);
	}

	public static function activeCheckBoxList($_model,$_attribute,$_data,$_htmlOptions=array())
	{
		
	}
	
	
	public static function activeRadioButtonList($_model,$_attribute,$_data,$_htmlOptions=array())
	{
		$value = self::encode( $_model->getData( $_attribute ) );
		if($_model->hasError( $_attribute) )
			self::addErrorCss( $_htmlOptions );
		return self::radioButtonList( get_class( $_model )."[{$_attribute}]" , $value , $_data , $_htmlOptions );
	}

	protected static function activeInputField($type,$model,$attribute,$htmlOptions)
	{
		$htmlOptions['type']=$type;
		$htmlOptions['name'] = get_class( $model )."[{$attribute}]";
		$value = $model->getData($attribute);
		if($type==='file')
			unset($htmlOptions['value']);
		else if(!isset($htmlOptions['value']))
			$htmlOptions['value']=$value;
		if($model->hasError($attribute))
			self::addErrorCss($htmlOptions);
		return self::tag('input',$htmlOptions);
	}
	
	/**
	 * Generates a text field input.
	 * @param string the input name
	 * @param string the input value
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} and {@link tag} for more details.)
	 * @return string the generated input field
	 * @see clientChange
	 * @see inputField
	 */
	public static function textField($name,$value='',$htmlOptions=array())
	{
		return self::inputField('text',$name,$value,$htmlOptions);
	}

	/**
	 * Generates a hidden input.
	 * @param string the input name
	 * @param string the input value
	 * @param array additional HTML attributes (see {@link tag}).
	 * @return string the generated input field
	 * @see inputField
	 */
	public static function hiddenField($name,$value='',$htmlOptions=array())
	{
		return self::inputField('hidden',$name,$value,$htmlOptions);
	}

	/**
	 * Generates a password field input.
	 * @param string the input name
	 * @param string the input value
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} and {@link tag} for more details.)
	 * @return string the generated input field
	 * @see clientChange
	 * @see inputField
	 */
	public static function passwordField($name,$value='',$htmlOptions=array())
	{
		return self::inputField('password',$name,$value,$htmlOptions);
	}

	/**
	 * Generates a file input.
	 * Note, you have to set the enclosing form's 'enctype' attribute to be 'multipart/form-data'.
	 * After the form is submitted, the uploaded file information can be obtained via $_FILES[$name] (see
	 * PHP documentation).
	 * @param string the input name
	 * @param string the input value
	 * @param array additional HTML attributes (see {@link tag}).
	 * @return string the generated input field
	 * @see inputField
	 */
	public static function fileField($name,$value='',$htmlOptions=array())
	{
		return self::inputField('file',$name,$value,$htmlOptions);
	}

	/**
	 * Generates a text area input.
	 * @param string the input name
	 * @param string the input value
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} and {@link tag} for more details.)
	 * @return string the generated text area
	 * @see clientChange
	 * @see inputField
	 */
	public static function textArea($name,$value='',$htmlOptions=array())
	{
		$htmlOptions['name']=$name;
		return self::tag('textarea',$htmlOptions,isset($htmlOptions['encode']) && !$htmlOptions['encode'] ? $value : self::encode($value));
	}

	/**
	 * Generates a radio button.
	 * @param string the input name
	 * @param boolean whether the check box is checked
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} and {@link tag} for more details.)
	 * @return string the generated radio button
	 * @see clientChange
	 * @see inputField
	 */
	public static function radioButton($name,$checked=false,$htmlOptions=array())
	{
		if($checked)
			$htmlOptions['checked']='checked';
		else
			unset($htmlOptions['checked']);
		$value=isset($htmlOptions['value']) ? $htmlOptions['value'] : 1;
		return self::inputField('radio',$name,$value,$htmlOptions);
	}

	/**
	 * Generates a check box.
	 * @param string the input name
	 * @param boolean whether the check box is checked
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} and {@link tag} for more details.)
	 * @return string the generated check box
	 * @see clientChange
	 * @see inputField
	 */
	public static function checkBox($name,$checked=false,$htmlOptions=array())
	{
		if($checked)
			$htmlOptions['checked']='checked';
		else
			unset($htmlOptions['checked']);
		$value=isset($htmlOptions['value']) ? $htmlOptions['value'] : 1;
		return self::inputField('checkbox',$name,$value,$htmlOptions);
	}

	/**
	 * Generates a drop down list.
	 * @param string the input name
	 * @param string the selected value
	 * @param array data for generating the list options (value=>display).
	 * You may use {@link listData} to generate this data.
	 * Please refer to {@link listOptions} on how this data is used to generate the list options.
	 * Note, the values and labels will be automatically HTML-encoded by this method.
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are recognized. See {@link clientChange} and {@link tag} for more details.
	 * In addition, the following options are also supported specifically for dropdown list:
	 * <ul>
	 * <li>prompt: string, specifies the prompt text shown as the first list option. Its value is empty.</li>
	 * <li>empty: string, specifies the text corresponding to empty selection. Its value is empty.
	 * Starting from version 1.0.10, the 'empty' option can also be an array of value-label pairs.
	 * Each pair will be used to render a list option at the beginning.</li>
	 * <li>options: array, specifies additional attributes for each OPTION tag.
	 *     The array keys must be the option values, and the array values are the extra
	 *     OPTION tag attributes in the name-value pairs. For example,
	 * <pre>
	 *     array(
	 *         'value1'=>array('disabled'=>true, 'label'=>'value 1'),
	 *         'value2'=>array('label'=>'value 2'),
	 *     );
	 * </pre>
	 *     This option has been available since version 1.0.3.
	 * </li>
	 * </ul>
	 * @return string the generated drop down list
	 * @see clientChange
	 * @see inputField
	 * @see listData
	 */
	public static function dropDownList($name,$select,$data,$htmlOptions=array())
	{
		$htmlOptions['name']=$name;
		$options="\n".self::listOptions($select,$data,$htmlOptions);
		return self::tag('select',$htmlOptions,$options);
	}

	/**
	 * Generates a list box.
	 * @param string the input name
	 * @param mixed the selected value(s). This can be either a string for single selection or an array for multiple selections.
	 * @param array data for generating the list options (value=>display)
	 * You may use {@link listData} to generate this data.
	 * Please refer to {@link listOptions} on how this data is used to generate the list options.
	 * Note, the values and labels will be automatically HTML-encoded by this method.
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized. See {@link clientChange} and {@link tag} for more details.
	 * In addition, the following options are also supported specifically for list box:
	 * <ul>
	 * <li>prompt: string, specifies the prompt text shown as the first list option. Its value is empty.</li>
	 * <li>empty: string, specifies the text corresponding to empty selection. Its value is empty.
	 * Starting from version 1.0.10, the 'empty' option can also be an array of value-label pairs.
	 * Each pair will be used to render a list option at the beginning.</li>
	 * <li>options: array, specifies additional attributes for each OPTION tag.
	 *     The array keys must be the option values, and the array values are the extra
	 *     OPTION tag attributes in the name-value pairs. For example,
	 * <pre>
	 *     array(
	 *         'value1'=>array('disabled'=>true, 'label'=>'value 1'),
	 *         'value2'=>array('label'=>'value 2'),
	 *     );
	 * </pre>
	 *     This option has been available since version 1.0.3.
	 * </li>
	 * </ul>
	 * @return string the generated list box
	 * @see clientChange
	 * @see inputField
	 * @see listData
	 */
	public static function listBox($name,$select,$data,$htmlOptions=array())
	{
		if(!isset($htmlOptions['size']))
			$htmlOptions['size']=4;
		if(isset($htmlOptions['multiple']))
		{
			if(substr($name,-2)!=='[]')
				$name.='[]';
		}
		return self::dropDownList($name,$select,$data,$htmlOptions);
	}
	
	/**
	 * Generates an input HTML tag.
	 * This method generates an input HTML tag based on the given input name and value.
	 * @param string the input type (e.g. 'text', 'radio')
	 * @param string the input name
	 * @param string the input value
	 * @param array additional HTML attributes for the HTML tag (see {@link tag}).
	 * @return string the generated input tag
	 */
	protected static function inputField($type,$name,$value,$htmlOptions)
	{
		$htmlOptions['type']=$type;
		$htmlOptions['value']=$value;
		$htmlOptions['name']=$name;
		return self::tag('input',$htmlOptions);
	}
	
/**
	 * Generates the list options.
	 * @param mixed the selected value(s). This can be either a string for single selection or an array for multiple selections.
	 * @param array the option data (see {@link listData})
	 * @param array additional HTML attributes. The following two special attributes are recognized:
	 * <ul>
	 * <li>encode: boolean, specifies whether to encode the values. Defaults to true. This option has been available since version 1.0.5.</li>
	 * <li>prompt: string, specifies the prompt text shown as the first list option. Its value is empty.</li>
	 * <li>empty: string, specifies the text corresponding to empty selection. Its value is empty.
	 * Starting from version 1.0.10, the 'empty' option can also be an array of value-label pairs.
	 * Each pair will be used to render a list option at the beginning.</li>
	 * <li>options: array, specifies additional attributes for each OPTION tag.
	 *     The array keys must be the option values, and the array values are the extra
	 *     OPTION tag attributes in the name-value pairs. For example,
	 * <pre>
	 *     array(
	 *         'value1'=>array('disabled'=>true, 'label'=>'value 1'),
	 *         'value2'=>array('label'=>'value 2'),
	 *     );
	 * </pre>
	 *     This option has been available since version 1.0.3.
	 * </li>
	 * </ul>
	 * @return string the generated list options
	 */
	public static function listOptions($selection,$listData,&$htmlOptions)
	{
		$raw=isset($htmlOptions['encode']) && !$htmlOptions['encode'];
		$content='';
		if(isset($htmlOptions['prompt']))
		{
			$content.='<option value="">'.($raw?$htmlOptions['prompt'] : self::encode($htmlOptions['prompt']))."</option>\n";
			unset($htmlOptions['prompt']);
		}
		if(isset($htmlOptions['empty']))
		{
			if(!is_array($htmlOptions['empty']))
				$htmlOptions['empty']=array(''=>$htmlOptions['empty']);
			foreach($htmlOptions['empty'] as $value=>$label)
			{
				if($raw)
					$content.='<option value="'.$value.'">'.$label."</option>\n";
				else
					$content.='<option value="'.self::encode($value).'">'.self::encode($label)."</option>\n";
			}
			unset($htmlOptions['empty']);
		}

		if(isset($htmlOptions['options']))
		{
			$options=$htmlOptions['options'];
			unset($htmlOptions['options']);
		}
		else
			$options=array();

		foreach($listData as $key=>$value)
		{
			if(is_array($value))
			{
				$content.='<optgroup label="'.($raw?$key : self::encode($key))."\">\n";
				$dummy=array('options'=>$options);
				if(isset($htmlOptions['encode']))
					$dummy['encode']=$htmlOptions['encode'];
				$content.=self::listOptions($selection,$value,$dummy);
				$content.='</optgroup>'."\n";
			}
			else
			{
				$attributes=array('value'=>(string)$key, 'encode'=>!$raw);
				if(!is_array($selection) && !strcmp($key,$selection) || is_array($selection) && in_array($key,$selection))
					$attributes['selected']='selected';
				if(isset($options[$key]))
					$attributes=array_merge($attributes,$options[$key]);
				$content.=self::tag('option',$attributes,$raw?(string)$value : self::encode((string)$value))."\n";
			}
		}
		return $content;
	}
	
	public static function radioButtonList($name,$select,$data,$htmlOptions=array())
	{
		$template=isset($htmlOptions['template'])?$htmlOptions['template']:'{input} {label}';
		$separator=isset($htmlOptions['separator'])?$htmlOptions['separator']:"<br/>\n";
		$container=isset($htmlOptions['container'])?$htmlOptions['container']:'span';
		unset($htmlOptions['template'],$htmlOptions['separator'],$htmlOptions['container']);

		$labelOptions=isset($htmlOptions['labelOptions'])?$htmlOptions['labelOptions']:array();
		unset($htmlOptions['labelOptions']);

		if(isset($htmlOptions['empty']))
		{
			if(!is_array($htmlOptions['empty']))
				$htmlOptions['empty']=array(''=>$htmlOptions['empty']);
			$data=array_merge($htmlOptions['empty'],$data);
			unset($htmlOptions['empty']);
		}

		$items=array();
		$baseID=isset($htmlOptions['baseID']) ? $htmlOptions['baseID'] : self::getIdByName($name);
		//unset($htmlOptions['baseID']);
		$id=0;
		foreach($data as $value=>$labelTitle)
		{
			$checked=!strcmp($value,$select);
			$htmlOptions['value']=$value;
			$htmlOptions['id']=$baseID.'_'.$id++;
			$option=self::radioButton($name,$checked,$htmlOptions);
			$beginLabel=self::openTag('label',$labelOptions);
			$label=self::label($labelTitle,$htmlOptions['id'],$labelOptions);
			$endLabel=self::closeTag('label');
			$items[]=strtr($template,array(
				'{input}'=>$option,
				'{beginLabel}'=>$beginLabel,
				'{label}'=>$label,
				'{labelTitle}'=>$labelTitle,
				'{endLabel}'=>$endLabel,
			));
		}
		if(empty($container))
			return implode($separator,$items);
		else
			return self::tag($container,array('id'=>$baseID),implode($separator,$items));
	}
	
	/**
	 * Normalizes the input parameter to be a valid URL.
	 *
	 * If the input parameter is an empty string, the currently requested URL will be returned.
	 *
	 * If the input parameter is a non-empty string, it is treated as a valid URL and will
	 * be returned without any change.
	 *
	 * If the input parameter is an array, it is treated as a controller route and a list of
	 * GET parameters, and the {@link CController::createUrl} method will be invoked to
	 * create a URL. In this case, the first array element refers to the controller route,
	 * and the rest key-value pairs refer to the additional GET parameters for the URL.
	 * For example, <code>array('post/list', 'page'=>3)</code> may be used to generate the URL
	 * <code>/index.php?r=post/list&page=3</code>.
	 *
	 * @param mixed the parameter to be used to generate a valid URL
	 * @param string the normalized URL
	 */
	public static function normalizeUrl($url)
	{
		if(is_array($url))
		{
			//
		}
		return $url==='' ? Nbt::app()->getRequest()->getUrl() : $url;
	}
	
	/**
	 * Renders the HTML tag attributes.
	 * @param array attributes to be rendered
	 * @return string the rendering result
	 * @since 1.0.5
	 */
	protected static function renderAttributes($htmlOptions)
	{
		if($htmlOptions===array())
			return '';
		$html='';
		$raw=isset($htmlOptions['encode']) && !$htmlOptions['encode'];
		unset($htmlOptions['encode']);
		if($raw)
		{
			foreach($htmlOptions as $name=>$value)
				$html .= ' ' . $name . '="' . $value . '"';
		}
		else
		{
			foreach((array)$htmlOptions as $name=>$value)
				$html .= ' ' . $name . '="' . self::encode($value) . '"';
		}
		return $html;
	}
	
	public static function encode( $_val )
	{
		return htmlspecialchars( $_val );
	}
	
	public static function decode( $_val )
	{
		return htmlspecialchars_decode( $_val );
	}
	
	/**
	 * 根据查询的字段，数据库名，以及查询条件返回条件对象
	 * @param $_strData string,$_strTable string,$_strCondition string
	 * @return CDbCriteria
	 * @author zhaojingyun
	 */
	public function makeCondition($_strData='',$_strTable='',$_strCondition='',$_strOrder= ''){
		$objCondition = new CDbCriteria();
		$objCondition->select = $_strData;
		$objCondition->from = $_strTable;
		$objCondition->condition = $_strCondition;
		$objCondition->order = $_strOrder;
		return $objCondition;
	}
//end class
}
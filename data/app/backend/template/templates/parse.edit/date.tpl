<label>
    {$field->getName()}:
    <input type='text' id='{$name}' value='{$field->get()|htmlentities:true:'UTF-8'}' class='date' name='{$arrayName}[{$name}]' readonly='readonly'>
</label>
<br />
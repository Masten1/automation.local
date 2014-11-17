<label>
    {$field->getName()}:
    <input type='text' id='{$name}' value='{$field->get()|htmlentities:true:'UTF-8'}' name='{$arrayName}[{$name}]'>
</label>
<br />
======================================================
Trait para el Manejo de Archivo asociados a modelos.
======================================================

Primer Pasos
############

- Colocar dentro de **app/Traits/** el archivo ModelFileManager.php
- Dentro del modelo sobre el que se desea aplicar colocar **use App\Traits\ModelFileManager**
- Luego de abierta la case colocar **use ModelFileManager** 
- Crear una variable protegida llamada $disk con el valor del disco creado para almacenar la info (en caso de no tener disco
si no que se realiza en el root o disk base, dejar en blanco o no crear) **Protected $disk = 'images';**

.. image:: Model-Exmple.png

Utilizacion
##########

Almacenar un archivo.
#####################
Para almacenar un archivo se utiliza la funcion **storeTheFile()** la cual acepta por parametro 3 valores:
- **$type** el cual acepta **put**, para indicar contenido proveniendo de un get or file_get_content y **store** para indicar contenido
proveniente de un request.
- **$field** el cual contendra el nombre del campo en el modelo a asociar.
- **$file** que contendra el contenido del archivo en caso de no provenir desde un request.

Ejemplo: 
Almacenar un archivo en el campo logo del modelo Company, el cual llega por request:


    public function update(StoreCompany $request, Company $company)
    {
        $company->update($request->input());
        if ($request->hasFile('logo')) {
            **$company->storeTheFile('store','logo');**
        }
        return redirect()->route('companies.index')->with('message', trans("msg.company_update"))->with('message_type', 'success');
    }

Nota, la funcion se encargara de verificar si el campo tiene algun valor, en caso de tenerlo borrara el archivo original y
procedera a almacenar el nuevo.

Eliminar un archivo.
#####################
Para eliminar un archivo se utiliza la funcion **deleteTheFile()** la cual acepta solo un parametro:
- **$field** para indicar el campo de el modelo a asociar.


Ejemplo:
Borrar un archivo en el campo logo del modelo Company.

$company = Company::find($id);
**$company->deleteTheFile('logo');**


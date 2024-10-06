@component('mail::message')
    # Erros de Importação

    Ocorreu um erro ao importar o arquivo. Abaixo estão os detalhes dos erros encontrados:


    @foreach ($errors as $line => $messages)

        * **Linha {{ $line }}**

        @foreach ($messages as $key => $message)
            - {{ $message }}
        @endforeach
        ***
    @endforeach

    Obrigado,<br>
    {{ config('app.name') }}
@endcomponent

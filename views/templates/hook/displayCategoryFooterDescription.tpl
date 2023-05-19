{if isset($footer_description) && !empty($footer_description)}
    <div class="flex gap-10">
        <div id="footer_description" class="max-w-prose text-sm">
            {$footer_description}
        </div>
        {* Todo implement the image upload *}
        <div class="flex-grow bg-cover bg-no-repeat bg-center -my-8 -mr-8" style="background-image: url({$modules_dir}genzo_category/views/img/example.webp);"></div>
    </div>
{/if}
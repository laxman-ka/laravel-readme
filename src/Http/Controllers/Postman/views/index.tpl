<div class="doc-hub-outer-container doc-hub-api">
    <div class="docs-hub">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item">API Reference</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row doc-hub-content-container">
            <div class="col doc-hub-sidebar" data-sticky="100">
                <div class="doc-hub-container">
                    <div class="doc-hub-items" id="doc-hub-indexes">
                        <ul role="side-nav">
                        {foreach $indexes as $item}
                        <li data-title="{$item.name}"><a href="{$item.link}">{$item.name}</a>
                            {if $item.childs}
                            <ul>
                                {foreach $item.childs as $child}
                                <li data-title="{$child.name}"><a href="{$child.link}"><span class="method {$child.method_slug}">{$child.method}</span>{$child.name}</a>
                                    {if $child.childs}
                                    <ul>
                                        {foreach $child.childs as $gchild}
                                        <li data-title="{$gchild.name}"><a href="{$gchild.link}"><span class="method {$gchild.method_slug}">{$gchild.method}</span>{$gchild.name}</a></li>
                                        {/foreach}
                                    </ul>
                                    {/if}
                                </li>
                                {/foreach}
                            </ul>
                            {/if}
                        </li>
                        {/foreach}
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col doc-hub-page">
                <div class="row">
                    <div class="col-12">
                        <div class="doc-hub-body">

                            <div class="row">
                                <div class="col-7">
                                    <div class="doc-hub-body-info">
                                        <h2>{$collection.info.name}</h2>
                                        <div class="hub-item-description">
                                            {$collection.info.description}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-5 doc-hub-code"></div>
                            </div>

                            {foreach $collection.item as $item}
                            <div class="row">
                                <div class="col-7">
                                    <div class="doc-hub-item-info">
                                        <h2 class="header-scroll header-scroll-2">
                                            <a id="{$item.slug}" class="section-anchor" href="#{$item.slug}"></a>
                                            {$item.name}
                                            <a class="fa fa-anchor hub-anchor" href="#{$item.slug}"></a>
                                        </h2>
                                        <div class="hub-item-description">
                                            {$item.description}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-5 doc-hub-code"></div>
                            </div>

                                {foreach $item.item as $child}
                            <div class="row">
                                <div class="col-7">
                                    <!-- request -->
                                    <div class="doc-hub-item">
                                        <h3 class="header-scroll header-scroll-3">
                                            <a id="{$child.slug}" class="section-anchor" href="#{$child.slug}"></a>
                                            {$child.name}
                                            <a class="fa fa-anchor hub-anchor" href="#{$child.slug}"></a>
                                        </h3>
                                        {if $child.request}
                                            <div class="hub-request-code">
                                                <pre><code>{$child.request.url.raw}</code></pre>
                                            </div>
                                            <div class="hub-item-description">
                                                {$child.request.description}
                                            </div>

                                            {if $child.request.header}
                                                <h4 class="doc-api-params-header">Headers</h4>
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {foreach $child.request.header as $header}
                                                        <tr>
                                                            <td class="doc-api-param-name">{$header.key}</td>
                                                            <td class="doc-api-param-value">{$header.value}</td>
                                                        </tr>
                                                        {/foreach}
                                                    </tbody>
                                                </table>
                                            {/if}

                                            {if $child.request.url.query}
                                                <h4 class="doc-api-params-header">Query Params</h4>
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {foreach $child.request.url.query as $query}
                                                        <tr>
                                                            <td class="doc-api-param-name">{$query.key}</td>
                                                            <td class="doc-api-param-value">{$query.value}</td>
                                                        </tr>
                                                        {/foreach}
                                                    </tbody>
                                                </table>
                                            {/if}

                                            {if $child.request.body}
                                                {if $child.request.body.mode eq 'formdata'}
                                                    <h4 class="doc-api-params-header">Body</h4>
                                                    <table>
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Value</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {foreach $child.request.body.formdata as $body}
                                                            <tr>
                                                                <td class="doc-api-param-name">{$body.key}<span>{$body.type}</span></td>
                                                                <td class="doc-api-param-value">{$body.value}</td>
                                                            </tr>
                                                            {/foreach}
                                                        </tbody>
                                                    </table>
                                                {/if}

                                                {if $child.request.body.mode eq 'urlencoded'}
                                                    <h4 class="doc-api-params-header">Body</h4>
                                                    <table>
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Value</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {foreach $child.request.body.urlencoded as $body}
                                                            <tr>
                                                                <td class="doc-api-param-name">{$body.key}<span>{$body.type}</span></td>
                                                                <td class="doc-api-param-value">{$body.value}</td>
                                                            </tr>
                                                            {/foreach}
                                                        </tbody>
                                                    </table>
                                                {/if}

                                                {if $child.request.body.mode eq 'raw' && $child.request.body.raw}
                                                <h4 class="doc-api-params-header">Body</h4>
                                                <pre><code>{$child.request.body.raw}</code></pre>
                                                {/if}

                                            {/if}

                                        {/if}
                                    </div>
                                        <!-- /request -->
                                </div>
                                <div class="col-5 doc-hub-code">

                                    <!-- request_code -->
                                    {if $child.request.code}
                                    <div class="doc-hub-response">
                                        <ul class="nav nav-tabs" role="tabs" data-language>
                                            {foreach $child.request.code as $key => $request}
                                              <li class="nav-item">
                                                <a class="nav-link {if $key eq 0}active {/if}" title="{$request.info.details}" data-toggle="tooltip" data-lang="{$request.info.key|lower}" href="#{$request.id}">{$request.info.title}</a>
                                              </li>
                                            {/foreach}
                                        </ul>
                                        <div class="tab-content">
                                            {foreach $child.request.code as $key => $request}
                                                <div id="{$request.id}" class="tab-pane {if $key eq 0}active {/if}">
                                                    <pre><code class="language language-{$request.info.key|lower}">{$request.code}</code></pre>
                                                </div>
                                            {/foreach}
                                        </div>
                                    </div>
                                    {/if}
                                    <!-- /request_code -->

                                    <!-- response -->
                                    {if $child.response}
                                    <div class="doc-hub-response">
                                        <ul class="nav nav-tabs" role="tabs">
                                            {foreach $child.response as $key => $response}
                                              <li class="nav-item">
                                                <a class="nav-link {if $key eq 0}active {/if}" href="#{$response.id}">{$response.name}</a>
                                              </li>
                                            {/foreach}
                                        </ul>
                                        <div class="tab-content">
                                            {foreach $child.response as $key => $response}
                                                <div id="{$response.id}" class="tab-pane {if $key eq 0}active {/if}">
                                                    <pre><code class="language json">{$response.body}</code></pre>
                                                </div>
                                            {/foreach}
                                        </div>
                                    </div>
                                    {/if}
                                    <!-- /response -->

                                </div>
                            </div>
                                {/foreach}

                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

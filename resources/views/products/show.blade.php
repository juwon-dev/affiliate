<x-app-layout>
    
    <main class="mobile-product-container">
        <div class="we-meant-container-mobile">
            <div></div>
            <p>The best value for your money all we listed here</p>
            <div></div>
        </div>
        
            
    
        <div class="product-home-container">
            <div class="product-container">
                
               {{ $product->name }}
               {{ $product->description }}
               {{ $product->content }}

            </div>
            
        </div>
    </main>

    <main>

        <div class="we-meant-container">
            <div></div>
            <p>The best value for your money all we listed here</p>
            <div></div>
        </div>

        <div class="outer-container-for-product">
            <div class="desktop-product-container">
                <div class="desktop-product-home-container">
                    <div class="product-container-d"></div>
                    {{ $product->name }}
                    {{ $product->description }}
                    {{ $product->content }}
                </div>
              
    
                
            
            </div>
    
            <div class="desktop-product-container">
                <div class="desktop-product-home-container">
                    <div class="product-container-d">
                        {{ $product->name }}
                        {{ $product->description }}
                        {{ $product->content }}
                    </div>
                
                </div>
    
            
            </div>
    
          
            
        </div>
    </main>



</x-app-layout>
@php
  $currentUrl = url()->current();
@endphp


@if (Route::currentRouteName() === 'faq')
  <script type="application/ld+json">
      {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "url": "{{ $currentUrl }}",
      "name": "Choose Your Plan - Go Ride",
      "description": "Select the plan that best fits your needs. Whether you're an individual or a business, Go Ride offers flexible pricing plans to meet your mobility needs.",
      "mainEntity": [
      {
      "@type": "Question",
      "name": "What is the taxi dispatch software?",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "Taxi booking software or taxi dispatch software is a system that enables taxi companies to track drivers and help drivers utilize some vital features, such as: GPS tracking, Automated payment process, Ride request management, Checking driver availability, Route optimization, Customer and driver request management."
      }
      },
      {
      "@type": "Question",
      "name": "Will Go Ride App suit small taxi companies?",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "Yes. Our Uber-like taxi dispatch system is suitable for all transport companies- small, medium, or large-scale Taxi/Cab ride businesses. You own a feature-rich app solution with the best UI/UX designs by choosing us."
      }
      },
      {
      "@type": "Question",
      "name": "How does a taxi dispatch software benefit taxi owners?",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "Using taxi dispatch software is a better way to manage your taxi business. It empowers your business with: Efficient Dispatching, Optimized Route Planning, Real-time tracking & dispatching capabilities, Data & analytics insights, Better customer service, Real-time data on driver performance & vehicle maintenance requirements."
      }
      },
      {
      "@type": "Question",
      "name": "Do you charge any additional costs on integrations for taxi booking software?",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "Our taxi scheduling software provides useful integrations with third-party services, such as payment gateways, mapping services, etc. If we need any additional costs, our team will explain why we are charging it during the customization process."
      }
      },
      {
      "@type": "Question",
      "name": "How quickly can I have my taxi booking software up and running?",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "It totally depends on the size of your company. For big transport companies, setting up the software and driver app in all their vehicles takes a little more time. Our team is set to meet your needs efficiently and on time."
      }
      },
      {
      "@type": "Question",
      "name": "Can I customize your taxi dispatch system to align it with your branding requirements?",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "Yes, you can customize the interface, colors, and logos to match your branding."
      }
      },
      {
      "@type": "Question",
      "name": "Do you need me to pay any set-up costs?",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "It will depend on implementation and configuration requirements. It will include initial customization, integration, and deployment for your business."
      }
      },
      {
      "@type": "Question",
      "name": "To what extent is your taxi fleet management software customizable?",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "Our pre-built taxi fleet management software is ready to use, but you can always add or remove features per your requirements."
      }
      },
      {
      "@type": "Question",
      "name": "Will you help us push our mobile application to the app market?",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "Yes, we will deploy the apps in Google Play and iTunes. You just need to provide your App Store credentials, and we will take care of the rest. For the admin panel and back-end setup, we will deploy it on your preferred server. We recommend using AWS as the server as it is the most reliable one."
      }
      },
      {
      "@type": "Question",
      "name": "How are we different from other similar taxi dispatch software solutions?",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "Captivating UI/UX design solutions to enrich your website UI & Interactive systems. Solution ready in 5 days as we have a few ready-made solutions. Free monthly post-delivery support to make your app compatible with the latest operating system version. Multi-Lingual support that allows you to use different, translated versions of your taxi app in 53 other languages. Multi-currency support for multiple currencies, independent of the site's languages."
      }
      },
      {
      "@type": "Question",
      "name": "Will I be able to accept payments through the software?",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "Yes. Simply add your bank account details in the Web Dispatch Panel provided by Go Ride Taxi. The Go Ride Fleet Taxi Management software supports a payment system that can directly transfer payments made by customers while booking rides to your Bank Account. All payment-related information, including your Bank Account details, will be stored in encrypted format for security purposes."
      }
      },
      {
      "@type": "Question",
      "name": "How can taxi scheduling software help you in your transportation business?",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "Using taxi scheduling software for transportation businesses has plenty of benefits. Some of the benefits are discussed as follows: Enhances Booking Frequency: Earlier, the taxi business was only limited to the areas where the company was situated. Taxi booking software can increase your booking limits, eventually increasing the number of bookings. Operational Effectiveness: Managing a taxi vehicle is difficult because one has to maintain goodwill in the business. Quality plays a key role when competition is high, and customer satisfaction is essential. Taxi booking software provides genuine reports, real-time updates, and other features to increase the efficiency and effectiveness of the dispatch process. Enhance Return on Investment: Because of this, more and more passengers are increasing daily. Having a higher rate of interest together with that also increases the business's profit. Other benefits include passenger security, multiple payment methods, and better customer satisfaction."
      }
      },
      {
      "@type": "Question",
      "name": "What features should I look for in airport shuttle dispatch software?",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "You should take advantage of some essential features of airport shuttle dispatch software. Listed below are some of the vital features: Booking management: The ability to efficiently manage bookings, reservations, and cancellations. Ride assignment: Automated ride assignments based on various criteria such as location, availability, and capacity. Real-time tracking: GPS tracking of vehicles and monitoring of ride statuses in real-time. Notifications and alerts: Automatic notifications and alerts to passengers and drivers about ride updates, delays, and cancellations. Reporting and analytics: Reporting and analytics features to track key performance indicators (KPIs) such as ride volumes, revenue, and customer feedback. Payment processing: Integrated payment processing capabilities to accept online payments from passengers. Mobile app: A passenger-facing mobile app that allows passengers to book rides, track their rides, and communicate with drivers."
      }
      },
      {
      "@type": "Question",
      "name": "What are all the payment options supported in a taxi dispatch system?",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "The Go Ride Taxi Management System supports offline as well as online payments. Online payments include payments using a credit card, debit card, net banking, and UPI payment."
      }
      }
      ]
      }
      </script>
@elseif(Route::currentRouteName() === 'privacy-policy')
  <script type="application/ld+json">
      {
      "@context": "https://schema.org",
      "@type": "WebPage",
      "url": "{{ $currentUrl }}",
      "name": "Privacy Policy - Go Ride",
      "description": "Read Go Ride's Privacy Policy to learn how we handle your personal data, the security measures in place, and how we protect your privacy.",
      "mainEntity": {
      "@type": "WebPage",
      "name": "Privacy Policy",
      "url": "{{ $currentUrl }}"
      },
      "sameAs": [
      "https://x.com/go_rides8499",
      "https://www.instagram.com/goride.run/",
      "https://www.facebook.com/people/Go-Rides/pfbid0346FYR2NnouGbc2Uq7PAuA4ccnV7rCxpQQs2XQgcJttannmZJUpfjgBWhoJZaZMNKl/"
      ],
      "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "5",
      "bestRating": "5",
      "worstRating": "0",
      "ratingCount": "1200"
      }
      }
      </script>
@elseif(Route::currentRouteName() === 'contact')
  <script type="application/ld+json">
      {
      "@context": "https://schema.org",
      "@type": "WebPage",
      "url": "{{ $currentUrl }}",
      "name": "Contact Us - Go Ride",
      "description": "Have any questions or feedback? Contact Go Ride customer support for assistance with rides, services, or any inquiries you may have.",
      "mainEntity": {
      "@type": "Organization",
      "name": "Go Ride",
      "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+919884557004",
      "contactType": "Customer Service",
      "areaServed": "Worldwide",
      "availableLanguage": "English,Tamil"
      },
      "sameAs": [
      "https://x.com/go_rides8499",
      "https://www.instagram.com/goride.run/",
      "https://www.facebook.com/people/Go-Rides/pfbid0346FYR2NnouGbc2Uq7PAuA4ccnV7rCxpQQs2XQgcJttannmZJUpfjgBWhoJZaZMNKl/"
      ]
      }
      }
      </script>
@elseif(Route::currentRouteName() === 'terms')
  <script type="application/ld+json">
      {
      "@context": "https://schema.org",
      "@type": "WebPage",
      "url": "{{ $currentUrl }}",
      "name": "Terms and Conditions - Go Ride",
      "description": "Read the Terms and Conditions governing the use of Go Ride's services, including user responsibilities, account policies, and payment terms.",
      "mainEntity": {
      "@type": "WebPage",
      "name": "Terms and Conditions",
      "url": "{{ $currentUrl }}"
      },
      "sameAs": [
      "https://x.com/go_rides8499",
      "https://www.instagram.com/goride.run/",
      "https://www.facebook.com/people/Go-Rides/pfbid0346FYR2NnouGbc2Uq7PAuA4ccnV7rCxpQQs2XQgcJttannmZJUpfjgBWhoJZaZMNKl/"
      ],
      "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "5",
      "bestRating": "5",
      "worstRating": "0",
      "ratingCount": "1200"
      }
      }
      </script>
@elseif(Route::currentRouteName() === 'about')
  <script type="application/ld+json">
      {
      "@context": "https://schema.org",
      "@type": "WebPage",
      "url": "{{ $currentUrl }}",
      "name": "About Us - Go Ride",
      "description": "Learn more about Go Ride, our mission to provide innovative mobility solutions, and how we are reshaping the transportation experience.",
      "mainEntity": {
      "@type": "Organization",
      "name": "Go Ride",
      "logo": "{{ url()->to('/') }}/goride/img/logo-dark.png",
      "url": "{{ url()->to('/') }}",
      "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+919884557004",
      "contactType": "Customer Service",
      "areaServed": "Worldwide",
      "availableLanguage": "English, Tamil"
      },
      "sameAs": [
      "https://x.com/go_rides8499",
      "https://www.instagram.com/goride.run/",
      "https://www.facebook.com/people/Go-Rides/pfbid0346FYR2NnouGbc2Uq7PAuA4ccnV7rCxpQQs2XQgcJttannmZJUpfjgBWhoJZaZMNKl/"
      ],
      "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "5",
      "bestRating": "5",
      "worstRating": "0",
      "ratingCount": "1200"
      }
      }
      }
      </script>
@elseif(Route::currentRouteName() === 'home')

  <script
    type="application/ld+json">      {      "@context": "https://schema.org",      "@type": "WebSite",      "url": "{{ url()->to('/') }}/",      "name": "Go Ride - The Heartbeat of Mobility",      "description": "Go Ride is your one-stop platform for cutting-edge mobility solutions. We offer a seamless ride experience with innovative technology, reliable vehicles, and a focus on customer satisfaction. Whether you're looking to book a ride, rent a vehicle, or explore sustainable transportation options, Go Ride makes it easier, faster, and smarter.",      "publisher": {      "@type": "Organization",      "name": "Go Ride",      "logo": "{{ url()->to('/') }}/goride/img/logo-dark.png",      "url": "{{ url()->to('/') }}",      "sameAs": [      "https://x.com/go_rides8499",      "https://www.instagram.com/goride.run/",      "https://www.facebook.com/people/Go-Rides/pfbid0346FYR2NnouGbc2Uq7PAuA4ccnV7rCxpQQs2XQgcJttannmZJUpfjgBWhoJZaZMNKl/",      "https://www.youtube.com/channel/UCK60VSKjbjLDhNlGzDCYDow"      ],      "contactPoint": {      "@type": "ContactPoint",      "telephone": "+919884557004",      "contactType": "Customer Service",      "areaServed": "Worldwide",      "availableLanguage": "English"      }      },      "potentialAction": {      "@type": "SearchAction",      "target": "{{ url()->to('/') }}/search?q={search_term}",      "query-input": "required name=search_term"      },      "aggregateRating": {      "@type": "AggregateRating",      "ratingValue": "5",      "bestRating": "5",      "worstRating": "0",      "ratingCount": "200"      }      }      "breadcrumb": {      "@type": "BreadcrumbList",      "itemListElement": [      {      "@type": "ListItem",      "position": 1,      "name": "Home",      "item": "{{ url()->to('/') }}/"      },      {      "@type": "ListItem",      "position": 2,      "name": "Our Features",      "item": "{{ url()->to('/') }}/features"      },      {      "@type": "ListItem",      "position": 3,      "name": "CRM With Dispatch",      "item": "{{ url()->to('/') }}/crm-with-dispatch"      },      {      "@type": "ListItem",      "position": 4,      "name": "Driver APP",      "item": "{{ url()->to('/') }}/driver-app"      },      {      "@type": "ListItem",      "position": 5,      "name": "Passenger APP",      "item": "{{ url()->to('/') }}/passenger-app"      },      {      "@type": "ListItem",      "position": 6,      "name": "Pricing",      "item": "{{ url()->to('/') }}/pricing"      },      {      "@type": "ListItem",      "position": 7,      "name": "About Us",      "item": "{{ url()->to('/') }}/about"      },      {      "@type": "ListItem",      "position": 8,      "name": "Contact Us",      "item": "{{ url()->to('/') }}/contact"      },      {      "@type": "ListItem",      "position": 9,      "name": "FAQ",      "item": "{{ url()->to('/') }}/faq"      },      {      "@type": "ListItem",      "position": 10,      "name": "Terms and Conditions",      "item": "{{ url()->to('/') }}/terms"      },      {      "@type": "ListItem",      "position": 11,      "name": "Privacy Policy",      "item": "{{ url()->to('/') }}/privacy-policy"      }      ]      }</script>







@elseif(Route::currentRouteName() === 'pricing')
  <script type="application/ld+json">
      {
      "@context": "https://schema.org",
      "@type": "WebPage",
      "url": "{{ $currentUrl }}",
      "name": "Choose Your Plan - Go Ride",
      "description": "Select the plan that best fits your needs. Whether you're an individual or a business, Go Ride offers flexible pricing plans to meet your mobility needs.",
      "mainEntity": {
      "@type": "Product",
      "name": "Go Ride Subscription Plans",
      "description": "Choose from flexible subscription plans for daily, monthly, or annual rides. Each plan is designed to provide the best value for your transportation needs.",
      "brand": {
      "@type": "Brand",
      "name": "Go Ride"
      },
      "offers": [
      {
      "@type": "Offer",
      "url": "{{ $currentUrl }}",
      "name": "Free Plan",
      "priceCurrency": "USD",
      "price": "0",
      "priceValidUntil": "{{ \Carbon\Carbon::now()->addYears(10)->format('Y-m-d') }}",
      "priceType": "Subscription",
      "itemCondition": "https://schema.org/NewCondition",
      "availability": "https://schema.org/InStock",
      "eligibleRegion": {
      "@type": "Place",
      "name": "Worldwide"
      }
      },
      {
      "@type": "Offer",
      "url": "{{ $currentUrl }}",
      "name": "Professional",
      "priceCurrency": "USD",
      "price": "19",
      "priceValidUntil": "{{ \Carbon\Carbon::now()->addYears(10)->format('Y-m-d') }}",
      "priceType": "Subscription",
      "itemCondition": "https://schema.org/NewCondition",
      "availability": "https://schema.org/InStock",
      "eligibleRegion": {
      "@type": "Place",
      "name": "Worldwide"
      }
      },
      {
      "@type": "Offer",
      "url": "{{ $currentUrl }}",
      "name": "Enterprise",
      "priceCurrency": "USD",
      "price": "39",
      "priceValidUntil": "{{ \Carbon\Carbon::now()->addYears(10)->format('Y-m-d') }}",
      "priceType": "Subscription",
      "itemCondition": "https://schema.org/NewCondition",
      "availability": "https://schema.org/InStock",
      "eligibleRegion": {
      "@type": "Place",
      "name": "Worldwide"
      }
      },
      {
      "@type": "Offer",
      "url": "{{ $currentUrl }}",
      "name": "Enterprise Yearly Plan",
      "priceCurrency": "USD",
      "price": "399",
      "priceValidUntil": "{{ \Carbon\Carbon::now()->addYears(10)->format('Y-m-d') }}",
      "priceType": "Subscription",
      "itemCondition": "https://schema.org/NewCondition",
      "availability": "https://schema.org/InStock",
      "eligibleRegion": {
      "@type": "Place",
      "name": "Worldwide"
      }
      },
      {
      "@type": "Offer",
      "url": "{{ $currentUrl }}",
      "name": "Professional Yearly Plan",
      "priceCurrency": "USD",
      "price": "199",
      "priceValidUntil": "{{ \Carbon\Carbon::now()->addYears(10)->format('Y-m-d') }}",
      "priceType": "Subscription",
      "itemCondition": "https://schema.org/NewCondition",
      "availability": "https://schema.org/InStock",
      "eligibleRegion": {
      "@type": "Place",
      "name": "Worldwide"
      }
      },
      {
      "@type": "Offer",
      "url": "{{ $currentUrl }}",
      "name": "Professional",
      "priceCurrency": "INR",
      "price": "99",
      "priceValidUntil": "{{ \Carbon\Carbon::now()->addYears(10)->format('Y-m-d') }}",
      "priceType": "Subscription",
      "itemCondition": "https://schema.org/NewCondition",
      "availability": "https://schema.org/InStock",
      "eligibleRegion": {
      "@type": "Place",
      "name": "Worldwide"
      }
      },
      {
      "@type": "Offer",
      "url": "{{ $currentUrl }}",
      "name": "Enterprise",
      "priceCurrency": "INR",
      "price": "499",
      "priceValidUntil": "{{ \Carbon\Carbon::now()->addYears(10)->format('Y-m-d') }}",
      "priceType": "Subscription",
      "itemCondition": "https://schema.org/NewCondition",
      "availability": "https://schema.org/InStock",
      "eligibleRegion": {
      "@type": "Place",
      "name": "Worldwide"
      }
      },
      {
      "@type": "Offer",
      "url": "{{ $currentUrl }}",
      "name": "Enterprise Yearly Plan",
      "priceCurrency": "INR",
      "price": "4999",
      "priceValidUntil": "{{ \Carbon\Carbon::now()->addYears(10)->format('Y-m-d') }}",
      "priceType": "Subscription",
      "itemCondition": "https://schema.org/NewCondition",
      "availability": "https://schema.org/InStock",
      "eligibleRegion": {
      "@type": "Place",
      "name": "Worldwide"
      }
      },
      {
      "@type": "Offer",
      "url": "{{ $currentUrl }}",
      "name": "Professional Yearly Plan",
      "priceCurrency": "INR",
      "price": "9999",
      "priceValidUntil": "{{ \Carbon\Carbon::now()->addYears(10)->format('Y-m-d') }}",
      "priceType": "Subscription",
      "itemCondition": "https://schema.org/NewCondition",
      "availability": "https://schema.org/InStock",
      "eligibleRegion": {
      "@type": "Place",
      "name": "Worldwide"
      }
      }
      ]
      }
      }

      </script>


@elseif(Route::currentRouteName() === 'custom-plan')
  <script type="application/ld+json">
    {    
    "@context": "https://schema.org",
    "@type": "WebSite",
    "url": "{{ url()->to('/') }}",
    "name": "Go Ride - The Heartbeat of Mobility",
    "description": "Go Ride is your one-stop platform for cutting-edge mobility solutions. We offer a seamless ride experience with innovative technology, reliable vehicles, and a focus on customer satisfaction. Whether you're looking to book a ride, rent a vehicle, or explore sustainable transportation options, Go Ride makes it easier, faster, and smarter.",
    "publisher": {
    "@type": "Organization",
    "name": "Go Ride",
    "logo": "{{ url()->to('/') }}/goride/img/logo-dark.png",
    "url": "{{ url()->to('/') }}",
    "sameAs": [
      "https://x.com/go_rides8499",
      "https://www.instagram.com/goride.run/",
      "https://www.facebook.com/people/Go-Rides/pfbid0346FYR2NnouGbc2Uq7PAuA4ccnV7rCxpQQs2XQgcJttannmZJUpfjgBWhoJZaZMNKl/",
      "https://www.youtube.com/channel/UCK60VSKjbjLDhNlGzDCYDow"
    ],

    "custome_plan":{

      "@type":"Organization",
      "url":"{{ url()->to('/') }}/custom-plan",
      "name":"Customize Your Own Limo & Cab, Taxi Dispatch Software | GoRide Dispatch Software System.",
      "description":"Create a custom limo & taxi dispatch software solution with GoRide. Our flexible platform allows you to design your own booking system, manage fleets, optimize routes, and improve customer experience. Tailor every feature to fit your specific business needs with GoRide's fully customizable software."
    },

    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+919884557004",
      "contactType": "Customer Service",
      "areaServed": "Worldwide",
      "availableLanguage": "English,Tamil"
    }
    }
    }
      </script>


@elseif(str_contains($currentUrl, 'car-rental'))

<!-- Product Schema -->
<script type="application/ld+json" data-react-helmet="true">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "{{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} Cabs",
  "category": "Cab Services",
  "url": "{{ $seoTags['canonical'] }}",
  "description": {!! json_encode($seoTags['wikiDes'] ?? '') !!},
  "itemReviewed": {
    "@type": "Car",
    "name": "{{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} Cabs"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.3",
    "reviewCount": "600"
  },
  "audience": {
    "@type": "Audience",
    "description": "555 Reviews",
    "audienceType": "Customer"
  },
  "offers": {
    "@type": "AggregateOffer",
    "lowPrice": "{{ $seoTags['mini_four_seater'] }}",
    "priceCurrency": "INR"
  }
}
</script>

<!-- Service Schema -->
<script type="application/ld+json" data-react-helmet="true">
{
  "@context": "https://schema.org",
  "@type": "Service",
  "name": "{{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} Cab & Taxi Service",
  "alternateName": {!! json_encode($seoTags['metaKeyword'] ?? '') !!},
  "provider": {
    "@type": "Organization",
    "name": "GoRide Cabs",
    "url": "{{ url()->to('/') }}/"
  },
  "serviceType": "Affordable one-way and round-trip {{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} taxi and cab services including {{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} cab price, taxi fare, and cab fare options.",
  "areaServed": {
    "@type": "Place",
    "name": "Chennai"
  },
  "availableChannel": {
    "@type": "ServiceChannel",
    "serviceUrl": "{{ $seoTags['canonical'] }}"
  },
  "offers": {
    "@type": "Offer",
    "priceCurrency": "INR",
    "price": "{{ $seoTags['mini_four_seater'] }}",
    "description": "Get the lowest {{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} cab fare starting from ₹{{ $seoTags['mini_four_seater'] }}. Check {{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} taxi price and book now.",
    "eligibleRegion": "{{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }}",
    "url": "{{ $seoTags['canonical'] }}"
  },
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "{{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} Cab Options",
    "itemListElement": [
      {
        "@type": "Offer",
        "name": "Standard Cab - {{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} Taxi Fare",
        "price": "₹{{ $seoTags['mini_four_seater'] }}",
        "priceCurrency": "INR"
      },
      {
        "@type": "Offer",
        "name": "Premium Cab - {{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} Cab Price",
        "price": "{{ $seoTags['fivezero_seater'] }}",
        "priceCurrency": "INR"
      }
    ]
  },
  "description": {!! json_encode($seoTags['wikiDes'] ?? '') !!}
}
</script>

<!-- Trip Schema -->
<script type="application/ld+json" data-react-helmet="true">
{
  "@context": "https://schema.org",
  "@type": "Trip",
  "name": "{{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} Cab Trip",
  "description": "Book {{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} cab services with various car options including Hatchback, Sedan, and SUVs like Innova and Ertiga. AC and non-AC options available with flexible pricing.",
  "itinerary": [
    { "@type": "Place", "name": "{{ ucfirst($explode[0]) }}" },
    { "@type": "Place", "name": "{{ ucfirst($explode[2]) }}" }
  ],
  "offers": [
    {
      "@type": "Offer",
      "name": "{{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} - Mini/HATCHBACK/4 Seater (Indica, Swift)",
      "description": "Compact car with 4 seats, AC. + ₹{{ $seoTags['mini_four_seater'] }} (All Charges Included). Extra km: ₹13 after {{ $seoTags['kms'] }} km.",
      "priceCurrency": "INR",
      "price": "{{ $seoTags['mini_four_seater'] }}",
      "eligibleQuantity": {
        "@type": "QuantitativeValue",
        "value": {{ $seoTags['kms'] }},
        "unitCode": "KMT"
      },
      "itemOffered": {
        "@type": "Service",
        "name": "Mini/HATCHBACK Cab Service",
        "provider": { "@type": "Organization", "name": "GoRide" }
      },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.3",
        "bestRating": "5",
        "ratingCount": "48",
        "itemReviewed": { "@type": "Car", "name": "{{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} - Mini/HATCHBACK/4 seater (Indica, Swift)" }
      }
    },
    {
      "@type": "Offer",
      "name": "{{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} - SEDAN/6 Seater (Dzire, Etios)",
      "description": "Spacious car with 6 seats, AC. + ₹{{ $seoTags['six_seater'] }} (All Charges Included). Extra km: ₹17 after {{ $seoTags['kms'] }} km.",
      "priceCurrency": "INR",
      "price": "{{ $seoTags['six_seater'] }}",
      "eligibleQuantity": {
        "@type": "QuantitativeValue",
        "value": {{ $seoTags['kms'] }},
        "unitCode": "KMT"
      },
      "itemOffered": {
        "@type": "Service",
        "name": "SEDAN Cab Service",
        "provider": { "@type": "Organization", "name": "GoRide" }
      },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4",
        "bestRating": "5",
        "ratingCount": "739",
        "itemReviewed": { "@type": "Car", "name": "{{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} - SEDAN/6 Seater (Dzire, Etios)" }
      }
    },
    {
      "@type": "Offer",
      "name": "{{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} - SUV (Xylo, Ertiga)",
      "description": "Premium car with 7 seats, AC. + ₹{{ $seoTags['seven_seater'] }} (All Charges Included). Extra km: ₹19 after {{ $seoTags['kms'] }} km.",
      "priceCurrency": "INR",
      "price": "{{ $seoTags['seven_seater'] }}",
      "eligibleQuantity": {
        "@type": "QuantitativeValue",
        "value": {{ $seoTags['kms'] }},
        "unitCode": "KMT"
      },
      "itemOffered": {
        "@type": "Service",
        "name": "SUV Cab Service",
        "provider": { "@type": "Organization", "name": "GoRide" }
      },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.3",
        "bestRating": "5",
        "ratingCount": "270",
        "itemReviewed": { "@type": "Car", "name": "{{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} - SUV (Xylo, Ertiga)" }
      }
    }
  ]
}
</script>


@endif





@if (isset($slug) && $slug != null && $slug != '')
 
<!-- BreadcrumbList Schema -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "GoRide.run",
  "url": "{{ url()->to('/') }}",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "{{ url()->to('/') }}"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "{{ $slug }}",
        "item": "{{ url()->current() }}"
      }
    ]
  },
  "mainEntity": {
    "@type": "Organization",
    "name": "GoRide",
    "url": "{{ url()->to('/') }}",
    "logo": "https://goride.run/goride/img/logo-dark.png",
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+919884557004",
      "contactType": "customer service",
      "contactOption": "HearingImpairedSupported",
      "areaServed": ["US", "GB", "CA", "IN"],
      "availableLanguage": "en"
    },
    "sameAs": [
      "https://www.facebook.com/people/Go-Rides/pfbid0zbEtpfJ8sejaaNFZ5tHym7bU53CCrM2qbRf4tPKa6fhG8L8mj4WhX7aMFuAV4ziJl/",
      "https://x.com/go_rides8499?mx=2",
      "https://www.instagram.com/goride.run/",
      "https://www.youtube.com/channel/UCK60VSKjbjLDhNlGzDCYDow",
      "{{ url()->to('/') }}"
    ]
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "5.0",
    "reviewCount": "1200"
  }
}
</script>

  @if (count($seoTags['faqData']) > 0)
    <!-- FAQ Schema -->
    <script type="application/ld+json">
    {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity":  [
      @foreach ($seoTags['faqData'] as $faq)
      {
      "@type": "Question",
      "name": "{{ $faq['question'] }}",
      "acceptedAnswer": {
      "@type": "Answer",
      "text": "{{ $faq['answer'] }}"
      }
      }@if (!$loop->last),@endif
  @endforeach
    ]
    }
    </script>

  @endif
  

@endif
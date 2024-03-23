import styled from 'styled-components/macro';
import tw, { theme } from 'twin.macro';

const SubNavigation = styled.div`
    ${tw`bg-zinc-800 mt-6 mb-3 rounded-full mx-auto px-3 max-w-5xl lg:w-[fit-content] overflow-x-auto container-snap`};

    & > div {
        ${tw`flex justify-center items-center text-sm mx-auto px-2`};

        & > a,
        & > div {
            ${tw`inline-block py-3 px-4 text-gray-400 font-semibold no-underline whitespace-nowrap transition-all duration-300`};

            &:not(:first-of-type) {
                ${tw`ml-2`};
            }

            &:hover {
                ${tw`text-neutral-100`};
                box-shadow: inset 0 1px ${theme`colors.green.700`.toString()};
            }

            &:active,
            &.active {
                ${tw`text-neutral-100`};
                box-shadow: inset 0 1px ${theme`colors.green.600`.toString()};
            }
        }
    }
`;

export default SubNavigation;

import tw, { css, styled } from 'twin.macro';

import { withSubComponents } from '@/components/helpers';
import { SiteTheme } from '@/state/theme';

const Wrapper = styled.div`
    ${tw`w-full flex flex-col px-4`};

    & > a {
        ${tw`h-10 w-full flex flex-row items-center text-neutral-300 cursor-pointer select-none px-4`};
        ${tw`hover:text-neutral-50`};

        & > svg {
            ${tw`h-6 w-6 flex flex-shrink-0`};
        }

        & > span {
            ${tw`font-header font-medium text-lg whitespace-nowrap leading-none ml-3`};
        }

        &:active,
        &.active {
            ${tw`text-neutral-50 bg-black/25 rounded-full`};
        }
    }
`;

const Section = styled.div`
    ${tw`h-[18px] font-header font-medium text-xs text-neutral-300 whitespace-nowrap uppercase ml-4 mb-1 select-none`};

    &:not(:first-of-type) {
        ${tw`mt-4`};
    }
`;

const User = styled.div`
    ${tw`h-16 w-full flex items-center bg-black/25 justify-center`};
`;

const Sidebar = styled.div<{ $collapsed?: boolean; theme: SiteTheme }>`
    ${tw`h-screen hidden md:flex flex-col items-center flex-shrink-0 overflow-x-hidden ease-linear`};
    ${tw`transition-all duration-500`};
    ${tw`w-[17.5rem]`};

    background-color: ${({ theme }) => theme.colors.sidebar};

    & > a {
        ${tw`h-10 w-full flex flex-row items-center text-neutral-300 cursor-pointer select-none px-8`};
        ${tw`hover:text-neutral-50`};

        & > svg {
            ${tw`transition-none h-6 w-6 flex flex-shrink-0`};
        }

        & > span {
            ${tw`font-header font-medium text-lg whitespace-nowrap leading-none ml-3`};
        }
    }

    ${props =>
        props.$collapsed &&
        css`
            ${tw`w-20`};

            ${Section} {
                ${tw`invisible`};
            }

            ${Wrapper} {
                ${tw`px-5`};

                & > a {
                    ${tw`justify-center px-0`};
                }
            }

            & > a {
                ${tw`justify-center px-4`};
            }

            & > a > span,
            ${User} > div,
            ${User} > a,
            ${Wrapper} > a > span {
                ${tw`hidden`};
            }
        `};
`;

export default withSubComponents(Sidebar, { Section, Wrapper, User });
